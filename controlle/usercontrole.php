<?php
require_once __DIR__ . '/../DAO/userdao.php';
require_once __DIR__ . '/../model/user.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$dao = new userdao();
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$errorMessage = '';
switch ($action) {
    case 'login':
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            header('Location: ../view/login.php?error=' . urlencode('Veuillez saisir votre email et votre mot de passe.') . '&email=' . urlencode($email));
            exit;
        }

        $loginUser = new user(null, null, $email, $password, null, null);
        $authenticatedUser = $dao->user_login($loginUser);

        if ($authenticatedUser) {
            $_SESSION['user'] = $authenticatedUser;
            header('Location: ../view/dashbord.php');
            exit;
        }

        header('Location: ../view/login.php?error=' . urlencode('Email ou mot de passe incorrect.') . '&email=' . urlencode($email));
        exit;
        



        
        break;
    case 'check_email':
        $email = trim($_GET['email'] ?? '');
        header('Content-Type: application/json; charset=utf-8');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'exists' => false]);
            exit;
        }

        $exists = $dao->getUserByEmail($email) ? true : false;
        echo json_encode(['success' => true, 'exists' => $exists]);
        exit;
    case 'logout':
        session_destroy();
        header('Location: ../view/login.php');
        exit;
        break;
    case 'update':
        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';

        if (empty($name) || empty($email)) {
            header('Location: ../view/dashbord.php?page=home&error=' . urlencode('Veuillez remplir tous les champs obligatoires.'));
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: ../view/dashbord.php?page=home&error=' . urlencode('Veuillez entrer une adresse e-mail valide.'));
            exit;
        }

        $user = new user($id, $name, $email, $password, $role, null);
        $dao->updateUser($user);
        header('Location: ../view/dashbord.php?page=home');
        exit;
        break;
    case 'delete':
        $id = $_GET['id'] ?? '';
        if (!empty($id)) {
            $dao->deleteUser($id);
        }
        header('Location: ../view/dashbord.php?page=home');
        exit;
        break;
    
    case 'create':
        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';
        $created_at = $_POST['created_at'] ?? date('Y-m-d H:i:s');
        if (empty($name) || empty($email) || empty($password)) {
            header('Location: ../view/index.php?error=' . urlencode('Veuillez remplir tous les champs obligatoires.') . '&name=' . urlencode($name) . '&email=' . urlencode($email));
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: ../view/index.php?error=' . urlencode('Veuillez entrer une adresse e-mail valide.') . '&name=' . urlencode($name));
            exit;
        }
        if (strlen($password) < 6) {
            header('Location: ../view/index.php?error=' . urlencode('Le mot de passe doit contenir au moins 6 caractères.') . '&name=' . urlencode($name) . '&email=' . urlencode($email));
            exit;
        }
        if ($dao->getUserByEmail($email)) {
            header('Location: ../view/index.php?error=' . urlencode('Un utilisateur avec cet e-mail existe déjà.') . '&name=' . urlencode($name) . '&email=' . urlencode($email));
            exit;
        }
        $user = new user($id, $name, $email, $password, $role, $created_at);
        $dao->createUser($user);
        header('Location: ../view/login.php?success=' . urlencode('Votre compte a été créé avec succès.')); 
        exit;
        break;
}
    
?>