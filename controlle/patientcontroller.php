<?php
require_once __DIR__ . '/../DAO/patientdao.php';
require_once __DIR__ . '/../model/patient.php';
require_once __DIR__ . '/../DAO/userdao.php';
require_once __DIR__ . '/../model/user.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function redirectToDashboard(array $params = []): void {
    $query = http_build_query($params);
    $location = '../view/dashbord.php' . ($query !== '' ? '?' . $query : '');
    header('Location: ' . $location);
    exit;
}

$dao = new patientdao();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $id = $_POST['id'] ?? '';
        $user_id = $_POST['user_id'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $birth_date = $_POST['birth_date'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $created_at = $_POST['created_at'] ?? date('Y-m-d H:i:s');

        if (empty($user_id) && !empty($_SESSION['user']) && is_object($_SESSION['user'])) {
            $user_id = $_SESSION['user']->getId();
        }

        if (empty($full_name) || empty($birth_date) || empty($phone) || empty($email)) {
            redirectToDashboard([
                'page' => 'patients',
                'view' => 'create',
                'error' => 'Veuillez remplir tous les champs obligatoires.',
                'full_name' => $full_name,
                'birth_date' => $birth_date,
                'phone' => $phone,
                'email' => $email,
            ]);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirectToDashboard([
                'page' => 'patients',
                'view' => 'create',
                'error' => 'Veuillez entrer une adresse e-mail valide.',
                'full_name' => $full_name,
                'birth_date' => $birth_date,
                'phone' => $phone,
                'email' => $email,
            ]);
        }

        $patient = new patient($id, $user_id, $full_name, $birth_date, $phone, $email, $created_at);
        if ($dao->addPatient($patient)) {
            redirectToDashboard(['page' => 'patients']);
        }
        redirectToDashboard(['page' => 'patients', 'error' => "Impossible d'ajouter le patient."]);
        break;

    case 'update':
        $id = $_POST['id'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $birth_date = $_POST['birth_date'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($full_name) || empty($birth_date) || empty($phone) || empty($email)) {
            redirectToDashboard([
                'page' => 'patients',
                'view' => 'update',
                'id' => $id,
                'error' => 'Veuillez remplir tous les champs obligatoires.',
                'full_name' => $full_name,
                'birth_date' => $birth_date,
                'phone' => $phone,
                'email' => $email,
            ]);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirectToDashboard([
                'page' => 'patients',
                'view' => 'update',
                'id' => $id,
                'error' => 'Veuillez entrer une adresse e-mail valide.',
                'full_name' => $full_name,
                'birth_date' => $birth_date,
                'phone' => $phone,
                'email' => $email,
            ]);
        }

        $patient = new patient($id, null, $full_name, $birth_date, $phone, $email, null);
        if ($dao->updatePatient($patient)) {
            redirectToDashboard(['page' => 'patients']);
        }
        redirectToDashboard(['page' => 'patients', 'error' => "Impossible de mettre à jour le patient."]);
        break;

    case 'delete':
        $id = $_GET['id'] ?? '';
        if (!empty($id)) {
            $dao->deletePatient($id);
        }
        redirectToDashboard(['page' => 'patients']);
        break;
}

redirectToDashboard(['page' => 'patients']);
?>