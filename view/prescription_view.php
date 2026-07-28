<?php
require_once __DIR__ . '/../DAO/prescriptiondao.php';
require_once __DIR__ . '/../DAO/patientdao.php';

$prescriptionDao = new prescriptiondao();
$patientDao = new patientdao();
$prescriptions = $prescriptionDao->getAllPrescriptions();
$patients = $patientDao->getAllPatients();
$view = $_GET['view'] ?? '';
$prescriptionToEdit = null;
$errors = [];
$errorMessage = $_GET['error'] ?? '';
$oldPatientId = $_GET['patient_id'] ?? '';
$oldMedicationName = $_GET['medication_name'] ?? '';
$oldDosage = $_GET['dosage'] ?? '';
$oldFrequency = $_GET['frequency'] ?? '';
$oldStartDate = $_GET['start_date'] ?? '';
$oldEndDate = $_GET['end_date'] ?? '';
$oldNotes = $_GET['notes'] ?? '';

if ($view === 'update') {
    $id = $_GET['id'] ?? '';
    if (!empty($id)) {
        $prescriptionToEdit = $prescriptionDao->getPrescriptionById($id);
        if (!$prescriptionToEdit) {
            $errors[] = 'Prescription introuvable.';
            $view = '';
        }
    } else {
        $errors[] = 'ID de prescription non fourni.';
        $view = '';
    }
}

$formMode = $view === 'update' ? 'update' : 'create';
$formTitle = $view === 'update' ? 'Modifier la prescription' : 'Ajouter une prescription';
$submitLabel = $view === 'update' ? 'Mettre à jour' : 'Créer';
$patientId = $prescriptionToEdit ? $prescriptionToEdit->getPatientId() : $oldPatientId;
$medicationName = $prescriptionToEdit ? $prescriptionToEdit->getMedicationName() : $oldMedicationName;
$dosage = $prescriptionToEdit ? $prescriptionToEdit->getDosage() : $oldDosage;
$frequency = $prescriptionToEdit ? $prescriptionToEdit->getFrequency() : $oldFrequency;
$startDate = $prescriptionToEdit ? $prescriptionToEdit->getStartDate() : $oldStartDate;
$endDate = $prescriptionToEdit ? $prescriptionToEdit->getEndDate() : $oldEndDate;
$notes = $prescriptionToEdit ? $prescriptionToEdit->getNotes() : $oldNotes;
$createdAt = $prescriptionToEdit ? $prescriptionToEdit->getCreatedAt() : date('Y-m-d H:i:s');
?>
    <!-- Contenu Prescriptions -->
    <?php if ($view === 'create' || $view === 'update'): ?>
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark"><?= $formTitle; ?></h5>
                <a href="?page=prescriptions" class="btn btn-sm btn-light border">Retour à la liste</a>
            </div>

            <?php if (!empty($errorMessage) || !empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php if (!empty($errorMessage)): ?>
                        <p class="mb-2"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($errors)): ?>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="../controlle/prescriptioncontroller.php?action=<?= $formMode; ?>">
                <?php if ($view === 'update'): ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($prescriptionToEdit->getId(), ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="patient_id" class="form-label">Patient</label>
                    <select id="patient_id" name="patient_id" class="form-select" required>
                        <option value="">Sélectionner un patient</option>
                        <?php foreach ($patients as $patient): ?>
                            <option value="<?= htmlspecialchars($patient->getId(), ENT_QUOTES, 'UTF-8'); ?>" <?= $patientId == $patient->getId() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($patient->getFullName(), ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="medication_name" class="form-label">Médicament</label>
                    <input id="medication_name" name="medication_name" type="text" class="form-control" required value="<?= htmlspecialchars($medicationName, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="dosage" class="form-label">Dosage</label>
                        <input id="dosage" name="dosage" type="text" class="form-control" required value="<?= htmlspecialchars($dosage, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="frequency" class="form-label">Fréquence</label>
                        <input id="frequency" name="frequency" type="text" class="form-control" required value="<?= htmlspecialchars($frequency, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Date de début</label>
                        <input id="start_date" name="start_date" type="date" class="form-control" required value="<?= htmlspecialchars($startDate, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">Date de fin</label>
                        <input id="end_date" name="end_date" type="date" class="form-control" value="<?= htmlspecialchars($endDate, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea id="notes" name="notes" class="form-control" rows="3"><?= htmlspecialchars($notes, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><?= $submitLabel; ?></button>
            </form>
        </div>
    <?php else: ?>
        <!-- Sous-navigation -->
        <div class="d-flex gap-2 nav-pills-custom mb-4">
            <a href="?page=prescriptions" class="nav-link active">Liste des prescriptions</a>
            <a href="?page=prescriptions&view=create" class="nav-link">Ajouter une prescription</a>
        </div>

        <!-- Conteneur Principal -->
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark">Gestion des prescriptions</h5>
                <button class="btn btn-sm btn-light border"><i class="bi bi-chevron-down"></i></button>
            </div>

            <!-- Barre d'actions -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted small fw-medium">
                    (<?= count($prescriptions); ?>) Enregistrements trouvés
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-custom-filter"><i class="bi bi-funnel me-1"></i> Filtrer</button>
                    <a href="?page=prescriptions&view=create" class="btn btn-sm btn-custom-add"><i class="bi bi-plus-lg me-1"></i> Ajouter</a>
                </div>
            </div>

            <!-- Tableau de données -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-secondary small">
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" class="form-check-input"></th>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Médicament</th>
                            <th>Dosage</th>
                            <th>Fréquence</th>
                            <th>Date de début</th>
                            <th>Date de fin</th>
                            <th>Créé le</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                     
                     <tbody>
                        <?php if (empty($prescriptions)) : ?>
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="bi bi-emoji-neutral fs-3 d-block mb-2"></i> Aucune prescription trouvée dans la base de données.
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($prescriptions as $prescription) : ?>
                                <?php 
                                    $patient = $patientDao->getPatientById($prescription->getPatientId());
                                    $patientName = $patient ? $patient->getFullName() : 'Patient inconnu';
                                ?>
                                <tr>
                                    <td><input type="checkbox" class="form-check-input"></td>
                                    <td class="fw-bold">#<?= htmlspecialchars($prescription->getId(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($patientName, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($prescription->getMedicationName(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($prescription->getDosage(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($prescription->getFrequency(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($prescription->getStartDate(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($prescription->getEndDate(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-secondary"><?= htmlspecialchars($prescription->getCreatedAt(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="?page=prescriptions&view=update&id=<?= $prescription->getId(); ?>" class="btn btn-link text-secondary p-1" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="../controlle/prescriptioncontroller.php?action=delete&id=<?= $prescription->getId(); ?>" class="btn btn-link text-danger p-1" title="Supprimer" onclick="return confirm('Supprimer cette prescription ?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
