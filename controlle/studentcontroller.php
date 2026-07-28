<?php
require_once __DIR__ . '/../DAO/studentdao.php';
require_once __DIR__ . '/../model/student.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function redirectToDashboard(array $params = []): void {
    $query = http_build_query($params);
    $location = '../view/dashbord.php' . ($query !== '' ? '?' . $query : '');
    header('Location: ' . $location);
    exit;
}

$dao = new studentdao();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $id = $_POST['id'] ?? '';
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $major = $_POST['major'] ?? '';
        $level = $_POST['level'] ?? '';
        $email = trim($_POST['email'] ?? '');

        if (empty($first_name) || empty($last_name) || empty($major) || empty($level) || empty($email)) {
            redirectToDashboard([
                'page' => 'students',
                'view' => 'create',
                'error' => 'Veuillez remplir tous les champs obligatoires.',
                'first_name' => $first_name,
                'last_name' => $last_name,
                'major' => $major,
                'level' => $level,
                'email' => $email,
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirectToDashboard([
                'page' => 'students',
                'view' => 'create',
                'error' => 'Veuillez entrer une adresse e-mail valide.',
                'first_name' => $first_name,
                'last_name' => $last_name,
                'major' => $major,
                'level' => $level,
                'email' => $email,
            ]);
        }

        $student = new Student($id, $first_name, $last_name, $major, $level, $email);
        if ($dao->createStudent($student)) {
            redirectToDashboard(['page' => 'students']);
        }
        redirectToDashboard(['page' => 'students', 'error' => "Impossible d'ajouter l'étudiant."]);
        break;

    case 'update':
        $id = $_POST['id'] ?? '';
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $major = $_POST['major'] ?? '';
        $level = $_POST['level'] ?? '';
        $email = trim($_POST['email'] ?? '');

        if (empty($first_name) || empty($last_name) || empty($major) || empty($level) || empty($email)) {
            redirectToDashboard([
                'page' => 'students',
                'view' => 'update',
                'id' => $id,
                'error' => 'Veuillez remplir tous les champs obligatoires.',
                'first_name' => $first_name,
                'last_name' => $last_name,
                'major' => $major,
                'level' => $level,
                'email' => $email,
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirectToDashboard([
                'page' => 'students',
                'view' => 'update',
                'id' => $id,
                'error' => 'Veuillez entrer une adresse e-mail valide.',
                'first_name' => $first_name,
                'last_name' => $last_name,
                'major' => $major,
                'level' => $level,
                'email' => $email,
            ]);
        }

        $student = new Student($id, $first_name, $last_name, $major, $level, $email);
        if ($dao->updateStudent($student)) {
            redirectToDashboard(['page' => 'students']);
        }
        redirectToDashboard(['page' => 'students', 'error' => "Impossible de mettre à jour l'étudiant."]);
        break;

    case 'delete':
        $id = $_GET['id'] ?? '';
        if (!empty($id)) {
            $dao->deleteStudent($id);
        }
        redirectToDashboard(['page' => 'students']);
        break;
}

redirectToDashboard(['page' => 'students']);
?>
