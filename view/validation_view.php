<?php
require_once __DIR__ . '/../DAO/validationdao.php';
require_once __DIR__ . '/../DAO/internshipdao.php';

$validationDao = new validationdao();
$internshipDao = new internshipdao();
$validations = $validationDao->getAllValidations();
$internships = $internshipDao->getAllInternships();
$view = $_GET['view'] ?? '';
$validationToEdit = null;
$errors = [];
$errorMessage = $_GET['error'] ?? '';
$oldInternshipId = $_GET['internship_id'] ?? '';
$oldDefenseDate = $_GET['defense_date'] ?? '';
$oldJuryMembers = $_GET['jury_members'] ?? '';
$oldFinalGrade = $_GET['final_grade'] ?? '';
$oldStatus = $_GET['status'] ?? '';

if ($view === 'update') {
    $id = $_GET['id'] ?? '';
    if (!empty($id)) {
        $validationToEdit = $validationDao->getValidationById($id);
        if (!$validationToEdit) {
            $errors[] = 'Validation introuvable.';
            $view = '';
        }
    } else {
        $errors[] = 'ID de validation non fourni.';
        $view = '';
    }
}

$formMode = $view === 'update' ? 'update' : 'create';
$formTitle = $view === 'update' ? 'Modifier la validation' : 'Ajouter une validation';
$submitLabel = $view === 'update' ? 'Mettre à jour' : 'Créer';
$internshipId = $validationToEdit ? $validationToEdit->getInternshipId() : $oldInternshipId;
$defenseDate = $validationToEdit ? $validationToEdit->getDefenseDate() : $oldDefenseDate;
$juryMembers = $validationToEdit ? $validationToEdit->getJuryMembers() : $oldJuryMembers;
$finalGrade = $validationToEdit ? $validationToEdit->getFinalGrade() : $oldFinalGrade;
$status = $validationToEdit ? $validationToEdit->getStatus() : $oldStatus;
?>
    <!-- Contenu Validations -->
    <?php if ($view === 'create' || $view === 'update'): ?>
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark"><?= $formTitle; ?></h5>
                <a href="?page=validations" class="btn btn-sm btn-light border">Retour à la liste</a>
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

            <form method="POST" action="../controlle/validationcontroller.php?action=<?= $formMode; ?>">
                <?php if ($view === 'update'): ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($validationToEdit->getId(), ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="internship_id" class="form-label">Stage concerné</label>
                    <select id="internship_id" name="internship_id" class="form-select" required>
                        <option value="">Sélectionner un stage</option>
                        <?php foreach ($internships as $internship): ?>
                            <option value="<?= htmlspecialchars($internship->getId(), ENT_QUOTES, 'UTF-8'); ?>" <?= $internshipId == $internship->getId() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($internship->getStudentName() . ' - ' . $internship->getCompanyName(), ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="defense_date" class="form-label">Date de soutenance</label>
                    <input id="defense_date" name="defense_date" type="date" class="form-control" value="<?= htmlspecialchars($defenseDate, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="mb-3">
                    <label for="jury_members" class="form-label">Membres du jury</label>
                    <input id="jury_members" name="jury_members" type="text" class="form-control" value="<?= htmlspecialchars($juryMembers, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="final_grade" class="form-label">Note finale (/20)</label>
                        <input id="final_grade" name="final_grade" type="number" step="0.01" min="0" max="20" class="form-control" value="<?= htmlspecialchars($finalGrade, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Statut</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="En cours" <?= $status == 'En cours' ? 'selected' : '' ?>>En cours</option>
                            <option value="Validé" <?= $status == 'Validé' ? 'selected' : '' ?>>Validé</option>
                            <option value="Non Validé" <?= $status == 'Non Validé' ? 'selected' : '' ?>>Non Validé</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><?= $submitLabel; ?></button>
            </form>
        </div>
    <?php else: ?>
        <!-- Sous-navigation -->
        <div class="d-flex gap-2 nav-pills-custom mb-4">
            <a href="?page=validations" class="nav-link active">Liste des validations</a>
            <a href="?page=validations&view=create" class="nav-link">Ajouter une validation</a>
        </div>

        <!-- Conteneur Principal -->
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark">Soutenances & Validations</h5>
                <button class="btn btn-sm btn-light border"><i class="bi bi-chevron-down"></i></button>
            </div>

            <!-- Barre d'actions -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted small fw-medium">
                    (<?= count($validations); ?>) Enregistrements trouvés
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-custom-filter"><i class="bi bi-funnel me-1"></i> Filtrer</button>
                    <a href="?page=validations&view=create" class="btn btn-sm btn-custom-add"><i class="bi bi-plus-lg me-1"></i> Ajouter</a>
                </div>
            </div>

            <!-- Tableau de données -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-secondary small">
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" class="form-check-input"></th>
                            <th>ID</th>
                            <th>Étudiant & Stage</th>
                            <th>Date de soutenance</th>
                            <th>Jury</th>
                            <th>Note /20</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                     
                     <tbody>
                        <?php if (empty($validations)) : ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-emoji-neutral fs-3 d-block mb-2"></i> Aucune validation trouvée dans la base de données.
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($validations as $validation) : ?>
                                <?php 
                                    $status = $validation->getStatus();
                                    $badgeClass = 'bg-secondary';
                                    if ($status === 'Validé') $badgeClass = 'bg-success';
                                    elseif ($status === 'En cours') $badgeClass = 'bg-warning text-dark';
                                    elseif ($status === 'Non Validé') $badgeClass = 'bg-danger';
                                ?>
                                <tr>
                                    <td><input type="checkbox" class="form-check-input"></td>
                                    <td class="fw-bold">#<?= htmlspecialchars($validation->getId(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($validation->getStudentName(), ENT_QUOTES, 'UTF-8'); ?></strong><br>
                                        <small class="text-muted"><i class="bi bi-building me-1"></i><?= htmlspecialchars($validation->getCompanyName(), ENT_QUOTES, 'UTF-8'); ?> (<?= htmlspecialchars($validation->getInternshipType(), ENT_QUOTES, 'UTF-8'); ?>)</small>
                                    </td>
                                    <td><?= $validation->getDefenseDate() ? htmlspecialchars($validation->getDefenseDate(), ENT_QUOTES, 'UTF-8') : '<span class="text-muted">Non planifiée</span>' ?></td>
                                    <td><?= htmlspecialchars($validation->getJuryMembers() ?? 'Aucun jury', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <?php if ($validation->getFinalGrade() !== null): ?>
                                            <strong><?= number_format($validation->getFinalGrade(), 2) ?> / 20</strong>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="?page=validations&view=update&id=<?= $validation->getId(); ?>" class="btn btn-link text-secondary p-1" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="../controlle/validationcontroller.php?action=delete&id=<?= $validation->getId(); ?>" class="btn btn-link text-danger p-1" title="Supprimer" onclick="return confirm('Supprimer cette validation ?');">
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