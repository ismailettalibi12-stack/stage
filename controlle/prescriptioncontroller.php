<?php
require_once __DIR__ . '/../DAO/prescriptiondao.php';
require_once __DIR__ . '/../model/prescription.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function redirectToDashboard(array $params = []): void {
    $query = http_build_query($params);
    $location = '../view/dashbord.php' . ($query !== '' ? '?' . $query : '');
    header('Location: ' . $location);
    exit;
}

$dao = new prescriptiondao();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $id = $_POST['id'] ?? '';
        $patient_id = $_POST['patient_id'] ?? '';
        $medication_name = trim($_POST['medication_name'] ?? '');
        $dosage = trim($_POST['dosage'] ?? '');
        $frequency = trim($_POST['frequency'] ?? '');
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $notes = trim($_POST['notes'] ?? '');
        $created_at = date('Y-m-d H:i:s');

        if (empty($patient_id) || empty($medication_name) || empty($dosage) || empty($frequency) || empty($start_date)) {
            redirectToDashboard([
                'page' => 'prescriptions',
                'view' => 'create',
                'error' => 'Veuillez remplir tous les champs obligatoires.',
                'patient_id' => $patient_id,
                'medication_name' => $medication_name,
                'dosage' => $dosage,
                'frequency' => $frequency,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'notes' => $notes,
            ]);
        }

        $prescription = new prescription($id, $patient_id, $medication_name, $dosage, $frequency, $start_date, $end_date, $notes, $created_at);
        if ($dao->addPrescription($prescription)) {
            redirectToDashboard(['page' => 'prescriptions']);
        }
        redirectToDashboard(['page' => 'prescriptions', 'error' => "Impossible d'ajouter la prescription."]);
        break;

    case 'update':
        $id = $_POST['id'] ?? '';
        $patient_id = $_POST['patient_id'] ?? '';
        $medication_name = trim($_POST['medication_name'] ?? '');
        $dosage = trim($_POST['dosage'] ?? '');
        $frequency = trim($_POST['frequency'] ?? '');
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $notes = trim($_POST['notes'] ?? '');

        if (empty($patient_id) || empty($medication_name) || empty($dosage) || empty($frequency) || empty($start_date)) {
            redirectToDashboard([
                'page' => 'prescriptions',
                'view' => 'update',
                'id' => $id,
                'error' => 'Veuillez remplir tous les champs obligatoires.',
                'patient_id' => $patient_id,
                'medication_name' => $medication_name,
                'dosage' => $dosage,
                'frequency' => $frequency,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'notes' => $notes,
            ]);
        }

        $prescription = new prescription($id, $patient_id, $medication_name, $dosage, $frequency, $start_date, $end_date, $notes, null);
        if ($dao->updatePrescription($prescription)) {
            redirectToDashboard(['page' => 'prescriptions']);
        }
        redirectToDashboard(['page' => 'prescriptions', 'error' => "Impossible de mettre à jour la prescription."]);
        break;

    case 'delete':
        $id = $_GET['id'] ?? '';
        if (!empty($id)) {
            $dao->deletePrescription($id);
        }
        redirectToDashboard(['page' => 'prescriptions']);
        break;
}

redirectToDashboard(['page' => 'prescriptions']);
?>
