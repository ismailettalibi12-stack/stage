<?php
require_once __DIR__ . '/../DAO/validationdao.php';
require_once __DIR__ . '/../model/validation.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function redirectToDashboard(array $params = []): void {
    $query = http_build_query($params);
    $location = '../view/dashbord.php' . ($query !== '' ? '?' . $query : '');
    header('Location: ' . $location);
    exit;
}

$dao = new validationdao();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $id = $_POST['id'] ?? '';
        $internship_id = $_POST['internship_id'] ?? '';
        $defense_date = $_POST['defense_date'] ?? '';
        $jury_members = trim($_POST['jury_members'] ?? '');
        $final_grade = $_POST['final_grade'] ?? '';
        $status = $_POST['status'] ?? 'En cours';

        if (empty($internship_id)) {
            redirectToDashboard([
                'page' => 'validations',
                'view' => 'create',
                'error' => 'Veuillez sélectionner un stage.',
                'internship_id' => $internship_id,
                'defense_date' => $defense_date,
                'jury_members' => $jury_members,
                'final_grade' => $final_grade,
                'status' => $status,
            ]);
        }

        $internship_id = (int)$internship_id;
        $final_grade = !empty($final_grade) ? (float)$final_grade : null;
        $defense_date = !empty($defense_date) ? $defense_date : null;
        $jury_members = !empty($jury_members) ? $jury_members : null;

        $validation = new Validation($id, $internship_id, $defense_date, $jury_members, $final_grade, $status);
        if ($dao->createValidation($validation)) {
            redirectToDashboard(['page' => 'validations']);
        }
        redirectToDashboard(['page' => 'validations', 'error' => "Impossible d'ajouter la validation."]);
        break;

    case 'update':
        $id = $_POST['id'] ?? '';
        $internship_id = $_POST['internship_id'] ?? '';
        $defense_date = $_POST['defense_date'] ?? '';
        $jury_members = trim($_POST['jury_members'] ?? '');
        $final_grade = $_POST['final_grade'] ?? '';
        $status = $_POST['status'] ?? 'En cours';

        if (empty($internship_id)) {
            redirectToDashboard([
                'page' => 'validations',
                'view' => 'update',
                'id' => $id,
                'error' => 'Veuillez sélectionner un stage.',
                'internship_id' => $internship_id,
                'defense_date' => $defense_date,
                'jury_members' => $jury_members,
                'final_grade' => $final_grade,
                'status' => $status,
            ]);
        }

        $internship_id = (int)$internship_id;
        $id = (int)$id;
        $final_grade = !empty($final_grade) ? (float)$final_grade : null;
        $defense_date = !empty($defense_date) ? $defense_date : null;
        $jury_members = !empty($jury_members) ? $jury_members : null;

        $validation = new Validation($id, $internship_id, $defense_date, $jury_members, $final_grade, $status);
        if ($dao->updateValidation($validation)) {
            redirectToDashboard(['page' => 'validations']);
        }
        redirectToDashboard(['page' => 'validations', 'error' => "Impossible de mettre à jour la validation."]);
        break;

    case 'delete':
        $id = $_GET['id'] ?? '';
        if (!empty($id)) {
            $dao->deleteValidation($id);
        }
        redirectToDashboard(['page' => 'validations']);
        break;
}

redirectToDashboard(['page' => 'validations']);
?>
