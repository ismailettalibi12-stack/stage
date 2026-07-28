<?php
require_once __DIR__ . '/../DAO/studentdao.php';

$studentDao = new studentdao();
$students = $studentDao->getAllStudents();
$view = $_GET['view'] ?? '';
$studentToEdit = null;
$errors = [];
$errorMessage = $_GET['error'] ?? '';
$oldFirstName = $_GET['first_name'] ?? '';
$oldLastName = $_GET['last_name'] ?? '';
$oldMajor = $_GET['major'] ?? '';
$oldLevel = $_GET['level'] ?? '';
$oldEmail = $_GET['email'] ?? '';

if ($view === 'update') {
    $id = $_GET['id'] ?? '';
    if (!empty($id)) {
        $studentToEdit = $studentDao->getStudentById($id);
        if (!$studentToEdit) {
            $errors[] = 'Étudiant introuvable.';
            $view = '';
        }
    } else {
        $errors[] = 'ID d\'étudiant non fourni.';
        $view = '';
    }
}

$formMode = $view === 'update' ? 'update' : 'create';
$formTitle = $view === 'update' ? 'Modifier l\'étudiant' : 'Ajouter un étudiant';
$submitLabel = $view === 'update' ? 'Mettre à jour' : 'Créer';
$firstName = $studentToEdit ? $studentToEdit->getFirstName() : $oldFirstName;
$lastName = $studentToEdit ? $studentToEdit->getLastName() : $oldLastName;
$major = $studentToEdit ? $studentToEdit->getMajor() : $oldMajor;
$level = $studentToEdit ? $studentToEdit->getLevel() : $oldLevel;
$email = $studentToEdit ? $studentToEdit->getEmail() : $oldEmail;
?>
    <!-- Contenu Étudiants -->
    <?php if ($view === 'create' || $view === 'update'): ?>
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark"><?= $formTitle; ?></h5>
                <a href="?page=students" class="btn btn-sm btn-light border">Retour à la liste</a>
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

            <form method="POST" action="../controlle/studentcontroller.php?action=<?= $formMode; ?>">
                <?php if ($view === 'update'): ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($studentToEdit->getId(), ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
                
                <div class="row mb-3">
                    <div class="col">
                        <label for="last_name" class="form-label">Nom</label>
                        <input id="last_name" name="last_name" type="text" class="form-control" required value="<?= htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col">
                        <label for="first_name" class="form-label">Prénom</label>
                        <input id="first_name" name="first_name" type="text" class="form-control" required value="<?= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="major" class="form-label">Filière</label>
                    <select id="major" name="major" class="form-select" required>
                        <option value="">Sélectionner une filière</option>
                        <option value="Génie Informatique" <?= $major == 'Génie Informatique' ? 'selected' : '' ?>>Génie Informatique</option>
                        <option value="Génie Civil" <?= $major == 'Génie Civil' ? 'selected' : '' ?>>Génie Civil</option>
                        <option value="Génie Électrique" <?= $major == 'Génie Électrique' ? 'selected' : '' ?>>Génie Électrique</option>
                        <option value="Génie Mécanique" <?= $major == 'Génie Mécanique' ? 'selected' : '' ?>>Génie Mécanique</option>
                        <option value="Autre" <?= $major == 'Autre' ? 'selected' : '' ?>>Autre</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="level" class="form-label">Niveau</label>
                    <select id="level" name="level" class="form-select" required>
                        <option value="">Sélectionner un niveau</option>
                        <option value="Classes Préparatoires (CPGE) - 1ère année" <?= $level == 'Classes Préparatoires (CPGE) - 1ère année' ? 'selected' : '' ?>>Classes Préparatoires (CPGE) - 1ère année</option>
                        <option value="Classes Préparatoires (CPGE) - 2ème année" <?= $level == 'Classes Préparatoires (CPGE) - 2ème année' ? 'selected' : '' ?>>Classes Préparatoires (CPGE) - 2ème année</option>
                        <option value="Cycle d'Ingénieur - 1ère année" <?= $level == "Cycle d'Ingénieur - 1ère année" ? 'selected' : '' ?>>Cycle d'Ingénieur - 1ère année</option>
                        <option value="Cycle d'Ingénieur - 2ème année" <?= $level == "Cycle d'Ingénieur - 2ème année" ? 'selected' : '' ?>>Cycle d'Ingénieur - 2ème année</option>
                        <option value="Cycle d'Ingénieur - 3ème année" <?= $level == "Cycle d'Ingénieur - 3ème année" ? 'selected' : '' ?>>Cycle d'Ingénieur - 3ème année</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" name="email" type="email" class="form-control" required value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <button type="submit" class="btn btn-primary"><?= $submitLabel; ?></button>
            </form>
        </div>
    <?php else: ?>
        <!-- Sous-navigation -->
        <div class="d-flex gap-2 nav-pills-custom mb-4">
            <a href="?page=students" class="nav-link active">Liste des étudiants</a>
            <a href="?page=students&view=create" class="nav-link">Ajouter un étudiant</a>
        </div>

        <!-- Conteneur Principal -->
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark">Gestion des étudiants</h5>
                <button class="btn btn-sm btn-light border"><i class="bi bi-chevron-down"></i></button>
            </div>

            <!-- Barre d'actions -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted small fw-medium">
                    (<?= count($students); ?>) Enregistrements trouvés
                </div>
                <div class="d-flex gap-2">
                    <input type="text" id="studentSearch" class="form-control form-control-sm" placeholder="Rechercher..." style="width: 200px;">
                    <button class="btn btn-sm btn-custom-filter"><i class="bi bi-funnel me-1"></i> Filtrer</button>
                    <a href="?page=students&view=create" class="btn btn-sm btn-custom-add"><i class="bi bi-plus-lg me-1"></i> Ajouter</a>
                </div>
            </div>

            <!-- Tableau de données -->
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="studentTable">
                    <thead class="table-light text-secondary small">
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" class="form-check-input"></th>
                            <th>ID</th>
                            <th>Nom & Prénom</th>
                            <th>Filière</th>
                            <th>Niveau</th>
                            <th>Email</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                     <tbody>
                        <?php if (empty($students)) : ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-emoji-neutral fs-3 d-block mb-2"></i> Aucun étudiant trouvé dans la base de données.
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($students as $student) : ?>
                                <tr>
                                    <td><input type="checkbox" class="form-check-input"></td>
                                    <td class="fw-bold">#<?= htmlspecialchars($student->getId(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><strong><?= htmlspecialchars($student->getLastName(), ENT_QUOTES, 'UTF-8'); ?></strong> <?= htmlspecialchars($student->getFirstName(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($student->getMajor(), ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <td><?= htmlspecialchars($student->getLevel(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($student->getEmail(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="?page=students&view=update&id=<?= $student->getId(); ?>" class="btn btn-link text-secondary p-1" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="../controlle/studentcontroller.php?action=delete&id=<?= $student->getId(); ?>" class="btn btn-link text-danger p-1" title="Supprimer" onclick="return confirm('Supprimer cet étudiant ?');">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('studentSearch');
    const studentTable = document.getElementById('studentTable');

    if (searchInput && studentTable) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = studentTable.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
});
</script>