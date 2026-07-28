<?php
require_once __DIR__ . '/../DAO/patientdao.php';

$patientDao = new patientdao();
$patients = $patientDao->getAllPatients();
$view = $_GET['view'] ?? '';
$patientToEdit = null;
$errors = [];
$errorMessage = $_GET['error'] ?? '';
$oldFullName = $_GET['full_name'] ?? '';
$oldBirthDate = $_GET['birth_date'] ?? '';
$oldPhone = $_GET['phone'] ?? '';
$oldEmail = $_GET['email'] ?? '';

if ($view === 'update') {
    $id = $_GET['id'] ?? '';
    if (!empty($id)) {
        $patientToEdit = $patientDao->getPatientById($id);
        if (!$patientToEdit) {
            $errors[] = 'Patient introuvable.';
            $view = '';
        }
    } else {
        $errors[] = 'ID de patient non fourni.';
        $view = '';
    }
}

$formMode = $view === 'update' ? 'update' : 'create';
$formTitle = $view === 'update' ? 'Modifier le patient' : 'Ajouter un patient';
$submitLabel = $view === 'update' ? 'Mettre à jour' : 'Créer';
$fullName = $patientToEdit ? $patientToEdit->getFullName() : $oldFullName;
$birthDate = $patientToEdit ? $patientToEdit->getBirthDate() : $oldBirthDate;
$phone = $patientToEdit ? $patientToEdit->getPhone() : $oldPhone;
$email = $patientToEdit ? $patientToEdit->getEmail() : $oldEmail;
$createdAt = $patientToEdit ? $patientToEdit->getCreatedAt() : date('Y-m-d H:i:s');
?>
    <!-- Contenu Patients extrait du Dashboard -->
    <?php if ($view === 'create' || $view === 'update'): ?>
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark"><?= $formTitle; ?></h5>
                <a href="?page=patients" class="btn btn-sm btn-light border">Retour à la liste</a>
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

            <form method="POST" action="../controlle/patientcontroller.php?action=<?= $formMode; ?>">
                <?php if ($view === 'update'): ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($patientToEdit->getId(), ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="full_name" class="form-label">Nom complet</label>
                    <input id="full_name" name="full_name" type="text" class="form-control" required value="<?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="mb-3">
                    <label for="birth_date" class="form-label">Date de naissance</label>
                    <input id="birth_date" name="birth_date" type="date" class="form-control" required value="<?= htmlspecialchars($birthDate, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Téléphone</label>
                    <input id="phone" name="phone" type="text" class="form-control" required value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" name="email" type="email" class="form-control" required value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="mb-3">
                    <label for="created_at" class="form-label">Créé le</label>
                    <input id="created_at" name="created_at" type="datetime-local" class="form-control" value="<?= htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <button type="submit" class="btn btn-primary"><?= $submitLabel; ?></button>
            </form>
        </div>
    <?php else: ?>
        <!-- Sous-navigation (Onglets du haut de votre image) -->
        <div class="d-flex gap-2 nav-pills-custom mb-4">
            <a href="?page=patients" class="nav-link active">Liste des patients</a>
            <a href="?page=patients&view=create" class="nav-link">Ajouter un patient</a>
            <a href="#" class="nav-link">Rapports</a>
        </div>

        <!-- Conteneur Principal Blanc -->
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark">Informations du patient</h5>
                <button class="btn btn-sm btn-light border"><i class="bi bi-chevron-down"></i></button>
            </div>

            <!-- Barre d'actions intermédiaires -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted small fw-medium">
                    (<?= count($patients); ?>) Enregistrements trouvés
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-custom-filter"><i class="bi bi-funnel me-1"></i> Filtrer</button>
                    <a href="?page=patients&view=create" class="btn btn-sm btn-custom-add"><i class="bi bi-plus-lg me-1"></i> Ajouter</a>
                </div>
            </div>

            <!-- Tableau de données style Maquette -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-secondary small">
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" class="form-check-input"></th>
                            <th>Image</th>
                            <th>ID</th>
                            <th>Nom complet</th>
                            <th>Date de naissance</th>
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th>Créé le</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                     
                     <tbody>
                        <?php if (empty($patients)) : ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-emoji-neutral fs-3 d-block mb-2"></i> Aucun patient trouvé dans la base de données.
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($patients as $patient) : ?>
                                <tr>
                                    <td><input type="checkbox" class="form-check-input"></td>
                                    <td>
                                        <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&q=80" 
                                             class="avatar-img" alt="avatar">
                                    </td>
                                    <td class="fw-bold">#<?= htmlspecialchars($patient->getId(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($patient->getFullName(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($patient->getBirthDate(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($patient->getPhone(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($patient->getEmail(), ENT_QUOTES, 'UTF-8'); ?></td>
                                     
                                    <td class="text-secondary"><?= htmlspecialchars($patient->getCreatedAt(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="?page=patients&view=update&id=<?= $patient->getId(); ?>" class="btn btn-link text-secondary p-1" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="../controlle/patientcontroller.php?action=delete&id=<?= $patient->getId(); ?>" class="btn btn-link text-danger p-1" title="Supprimer" onclick="return confirm('Supprimer ce patient ?');">
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
