<?php
require_once __DIR__ . '/../DAO/pecdao.php';
require_once __DIR__ . '/../DAO/patientdao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function redirectToPec(string $message = '', string $type = 'error'): void {
    $query = $type === 'success' ? 'success=' . urlencode($message) : 'error=' . urlencode($message);
    header('Location: ../view/dashbord.php?page=pec&' . $query);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$pecDao = new pecdao();
$patientDao = new patientdao();

switch ($action) {
    case 'create':
        $patientId = $_POST['patient_id'] ?? '';
        $datePec = $_POST['date_pec'] ?? '';
        $organisme = trim($_POST['organisme'] ?? '');
        $statut = trim($_POST['statut'] ?? 'En attente');

        if (empty($patientId) || empty($datePec) || empty($organisme)) {
            redirectToPec('Veuillez remplir tous les champs obligatoires.');
        }

        $created = $pecDao->createPecRequest($patientId, $datePec, $organisme, $statut);
        if ($created) {
            redirectToPec('Demande PEC enregistrée avec succès.', 'success');
        }

        redirectToPec("Impossible d'enregistrer la demande PEC.");
        break;

    case 'update':
        $id = $_POST['id'] ?? '';
        $patientId = $_POST['patient_id'] ?? '';
        $datePec = $_POST['date_pec'] ?? '';
        $organisme = trim($_POST['organisme'] ?? '');
        $statut = trim($_POST['statut'] ?? 'En attente');

        if (empty($id) || empty($patientId) || empty($datePec) || empty($organisme)) {
            redirectToPec('Veuillez remplir tous les champs obligatoires.');
        }

        $updated = $pecDao->updatePecRequest($id, $patientId, $datePec, $organisme, $statut);
        if ($updated) {
            redirectToPec('Demande PEC mise à jour avec succès.', 'success');
        }

        redirectToPec("Impossible de mettre à jour la demande PEC.");
        break;

    case 'delete':
        $id = $_GET['id'] ?? '';
        if (!empty($id)) {
            $deleted = $pecDao->deletePecRequest($id);
            if ($deleted) {
                redirectToPec('Demande PEC supprimée avec succès.', 'success');
            }
        }
        redirectToPec("Impossible de supprimer la demande PEC.");
        break;

    default:
        header('Location: ../view/dashbord.php?page=pec');
        exit;
}