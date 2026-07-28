<?php
require_once __DIR__ . '/../model/user.php';
require_once __DIR__ . '/../DAO/userdao.php';
require_once __DIR__ . '/../DAO/pecdao.php';
require_once __DIR__ . '/../DAO/patientdao.php';
require_once __DIR__ . '/../DAO/stockdao.php';
require_once __DIR__ . '/../DAO/prescriptiondao.php';
require_once __DIR__ . '/../DAO/studentdao.php';
require_once __DIR__ . '/../DAO/internshipdao.php';
require_once __DIR__ . '/../DAO/validationdao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification de l'utilisateur connecté
if (empty($_SESSION['user']) || !is_object($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$currentUser = $_SESSION['user'];
if ($currentUser instanceof __PHP_Incomplete_Class) {
    unset($_SESSION['user']);
    header('Location: login.php');
    exit;
}

// Instanciation des DAO
$dao = new userdao();
$users = $dao->getAllUsers();

$pecDao = new pecdao();
$pecList = $pecDao->getAllPecRequests(); // utilisé dans pec_view.php

$patientDao = new patientdao();
$stockDao = new stockdao();
$prescriptionDao = new prescriptiondao();
$studentDao = new studentdao();
$internshipDao = new internshipdao();
$validationDao = new validationdao();

// Récupération de la page active (par défaut 'home')
$page = $_GET['page'] ?? 'home';

// Compteurs avec vérification de l'existence des méthodes
$totalUsers = count($users);
$totalPatients = method_exists($patientDao, 'countPatients') ? $patientDao->countPatients() : 0;
$totalStock = method_exists($stockDao, 'countStock') ? $stockDao->countStock() : 0;
$totalPrescriptions = method_exists($prescriptionDao, 'countPrescriptions') ? $prescriptionDao->countPrescriptions() : 0;
// $totalInvoices non utilisé, supprimé

// KPI Data pour le tableau de bord
$totalStudents = method_exists($studentDao, 'countStudents') ? $studentDao->countStudents() : 0;
$totalInternships = method_exists($internshipDao, 'countInternships') ? $internshipDao->countInternships() : 0;
$totalActiveInternships = method_exists($internshipDao, 'countActiveInternships') ? $internshipDao->countActiveInternships() : 0;
$totalLowStock = method_exists($stockDao, 'countLowStock') ? $stockDao->countLowStock() : 0;
$totalPecRequests = method_exists($pecDao, 'countPecRequests') ? $pecDao->countPecRequests() : 0;
$totalPendingPec = method_exists($pecDao, 'countPendingPecRequests') ? $pecDao->countPendingPecRequests() : 0;
$lowStockItems = method_exists($stockDao, 'getLowStockItems') ? $stockDao->getLowStockItems() : [];

// Advanced KPI Data
$totalStockValue = method_exists($stockDao, 'getTotalStockValue') ? $stockDao->getTotalStockValue() : 0;
$stockByCategory = method_exists($stockDao, 'getStockByCategory') ? $stockDao->getStockByCategory() : [];
$totalValidations = method_exists($validationDao, 'countValidations') ? $validationDao->countValidations() : 0;
$validationSuccessRate = method_exists($validationDao, 'getValidationSuccessRate') ? $validationDao->getValidationSuccessRate() : 0;
$upcomingDefenses = method_exists($validationDao, 'getUpcomingDefenses') ? $validationDao->getUpcomingDefenses(7) : [];

// Vérification supplémentaire pour la méthode getName() de l'utilisateur
$userName = (method_exists($currentUser, 'getName')) ? $currentUser->getName() : 'Utilisateur';
$userInitial = mb_substr($userName, 0, 1, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord — MVC Gestion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
     
   <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; color: #333; }
        :root { --main-crimson: #b02a2a; }
        
        .sidebar { width: 260px; min-height: 100vh; background: #ffffff; border-right: 1px solid #eaeaea; }
        .sidebar .nav-link { color: #6c757d; font-weight: 500; border-radius: 8px; margin-bottom: 5px; padding: 10px 15px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: var(--main-crimson); color: white !important; }
        .sidebar-section-title { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #adb5bd; font-weight: 700; margin-top: 20px; margin-bottom: 10px; padding-left: 15px; }
        .topbar { background-color: var(--main-crimson); color: white; height: 70px; }
        
        .nav-pills-custom .nav-link { color: #b02a2a; background-color: #f8f9fa; border: 1px solid #eaeaea; border-radius: 20px; padding: 8px 20px; font-weight: 500; }
        .nav-pills-custom .nav-link.active { background-color: #fce8e6; color: #b02a2a; border-color: #fce8e6; }
        .btn-custom-filter { border: 1px solid #b02a2a; color: #b02a2a; border-radius: 20px; padding: 6px 20px; }
        .btn-custom-add { background-color: #fce8e6; color: #b02a2a; border: none; border-radius: 20px; padding: 6px 20px; font-weight: 600; }
        .avatar-img { width: 35px; height: 35px; object-fit: cover; border-radius: 50%; }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- SIDEBAR -->
    <nav class="sidebar p-3 d-none d-md-block">
        <div class="d-flex align-items-center gap-2 px-2 mb-4">
            <i class="bi bi-heart-pulse-fill fs-3 text-danger"></i>
            <span class="fw-bold fs-5 text-dark">MYDIALYSE</span>
        </div>
        
        <div class="mb-3 px-2">
            <input type="text" id="menuSearch" class="form-control form-control-sm bg-light" placeholder="Recherche menu...">
        </div>

        <div class="sidebar-section-title">Général</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="?page=home" class="nav-link <?= $page == 'home' ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2-fill me-2"></i> Tableau de bord
                </a>
            </li>
        </ul>

        <div class="sidebar-section-title">Patients</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="?page=patients" class="nav-link <?= $page == 'patients' ? 'active' : '' ?>">
                    <i class="bi bi-people me-2"></i> Patients
                </a>
            </li>
            <li class="nav-item"><a href="?page=pris-en-charge" class="nav-link <?= $page == 'pris-en-charge' || $page == 'pec' ? 'active' : '' ?>"><i class="bi bi-receipt me-2"></i> prise en charge</a></li>
            <li class="nav-item"><a href="?page=prescriptions" class="nav-link <?= $page == 'prescriptions' ? 'active' : '' ?>"><i class="bi bi-file-earmark-medical me-2"></i> Prescriptions</a></li>
            <li class="nav-item"><a href="?page=stock" class="nav-link <?= $page == 'stock' ? 'active' : '' ?>"><i class="bi bi-box-seam me-2"></i> Stock</a></li>
        </ul>

        <div class="sidebar-section-title">Étudiants</div>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="?page=students" class="nav-link <?= $page == 'students' ? 'active' : '' ?>"><i class="bi bi-person-badge me-2"></i> Étudiants</a></li>
            <li class="nav-item"><a href="?page=internships" class="nav-link <?= $page == 'internships' ? 'active' : '' ?>"><i class="bi bi-briefcase me-2"></i> Stages</a></li>
            <li class="nav-item"><a href="?page=validations" class="nav-link <?= $page == 'validations' ? 'active' : '' ?>"><i class="bi bi-check-circle me-2"></i> Validations</a></li>
        </ul>
    </nav>

    <!-- CONTENU PRINCIPAL -->
    <div class="flex-grow-1">
        <!-- TOPBAR -->
        <header class="topbar d-flex align-items-center justify-content-between px-4 shadow-sm">
            <h2 class="h5 mb-0 fw-semibold">
                <?php
                // Titre dynamique selon la page
                $titles = [
                    'patients'      => '<i class="bi bi-people-fill me-2"></i>Patients',
                    'pris-en-charge' => '<i class="bi bi-receipt me-2"></i>Prise en charge',
                    'pec'           => '<i class="bi bi-receipt me-2"></i>Prise en charge',
                    'prescriptions' => '<i class="bi bi-file-earmark-medical me-2"></i>Prescriptions',
                    'stock'         => '<i class="bi bi-box-seam me-2"></i>Stock',
                    'students'      => '<i class="bi bi-person-badge me-2"></i>Étudiants',
                    'internships'   => '<i class="bi bi-briefcase me-2"></i>Stages',
                    'validations'   => '<i class="bi bi-check-circle me-2"></i>Validations',
                    'home'          => '<i class="bi bi-grid-3x3-gap-fill me-2"></i>Tableau de bord'
                ];
                echo isset($titles[$page]) ? $titles[$page] : $titles['home'];
                ?>
            </h2>
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-bell fs-5"></i>
                <div class="bg-light rounded-circle text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px;">
                    <?= htmlspecialchars($userInitial, ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <span class="small d-none d-sm-inline"><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></span>
                <a href="../controlle/usercontrole.php?action=logout" class="btn btn-sm btn-outline-light ms-2">Déconnexion</a>
            </div>
        </header>

        <!-- ROUTAGE DES VUES -->
        <main class="p-4">
            <?php
            switch ($page) {
                case 'patients':
                    include __DIR__ . '/../view/patients.php';
                    break;
                case 'pris-en-charge':
                case 'pec':
                    include __DIR__ . '/../view/pec_view.php';
                    break;
                case 'prescriptions':
                    include __DIR__ . '/../view/prescription_view.php';
                    break;
                case 'stock':
                    include __DIR__ . '/../view/stock_view.php';
                    break;
                case 'students':
                    include __DIR__ . '/../view/student_view.php';
                    break;
                case 'internships':
                    include __DIR__ . '/../view/internship_view.php';
                    break;
                case 'validations':
                    include __DIR__ . '/../view/validation_view.php';
                    break;
                case 'home':
                default:
                    include __DIR__ . '/../view/home_view.php'; // Correction du chemin
                    break;
            }
            ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>