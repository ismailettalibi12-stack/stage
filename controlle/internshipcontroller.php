<?php
require_once __DIR__ . '/../DAO/internshipdao.php';
require_once __DIR__ . '/../model/internship.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function redirectToDashboard(array $params = []): void {
    $query = http_build_query($params);
    $location = '../view/dashbord.php' . ($query !== '' ? '?' . $query : '');
    header('Location: ' . $location);
    exit;
}

$dao = new internshipdao();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $id = $_POST['id'] ?? '';
        $student_id = $_POST['student_id'] ?? '';
        $company_name = trim($_POST['company_name'] ?? '');
        $type = $_POST['type'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $tech_stack = trim($_POST['tech_stack'] ?? '');

        if (empty($student_id) || empty($company_name) || empty($type) || empty($start_date) || empty($end_date)) {
            redirectToDashboard([
                'page' => 'internships',
                'view' => 'create',
                'error' => 'Veuillez remplir tous les champs obligatoires.',
                'student_id' => $student_id,
                'company_name' => $company_name,
                'type' => $type,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'tech_stack' => $tech_stack,
            ]);
        }

        $student_id = (int)$student_id;
        $internship = new Internship($id, $student_id, $company_name, $type, $start_date, $end_date, $tech_stack);
        if ($dao->createInternship($internship)) {
            redirectToDashboard(['page' => 'internships']);
        }
        redirectToDashboard(['page' => 'internships', 'error' => "Impossible d'ajouter le stage."]);
        break;

    case 'update':
        $id = $_POST['id'] ?? '';
        $student_id = $_POST['student_id'] ?? '';
        $company_name = trim($_POST['company_name'] ?? '');
        $type = $_POST['type'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $tech_stack = trim($_POST['tech_stack'] ?? '');

        if (empty($student_id) || empty($company_name) || empty($type) || empty($start_date) || empty($end_date)) {
            redirectToDashboard([
                'page' => 'internships',
                'view' => 'update',
                'id' => $id,
                'error' => 'Veuillez remplir tous les champs obligatoires.',
                'student_id' => $student_id,
                'company_name' => $company_name,
                'type' => $type,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'tech_stack' => $tech_stack,
            ]);
        }

        $student_id = (int)$student_id;
        $id = (int)$id;
        $internship = new Internship($id, $student_id, $company_name, $type, $start_date, $end_date, $tech_stack);
        if ($dao->updateInternship($internship)) {
            redirectToDashboard(['page' => 'internships']);
        }
        redirectToDashboard(['page' => 'internships', 'error' => "Impossible de mettre à jour le stage."]);
        break;

    case 'delete':
        $id = $_GET['id'] ?? '';
        if (!empty($id)) {
            $dao->deleteInternship($id);
        }
        redirectToDashboard(['page' => 'internships']);
        break;
}

redirectToDashboard(['page' => 'internships']);
?>
