<?php
require_once __DIR__ . '/../DAO/internshipdao.php';
require_once __DIR__ . '/../DAO/studentdao.php';

$internshipDao = new internshipdao();
$studentDao = new studentdao();
$internships = $internshipDao->getAllInternships();
$students = $studentDao->getAllStudents();
$view = $_GET['view'] ?? '';
$internshipToEdit = null;
$errors = [];
$errorMessage = $_GET['error'] ?? '';
$oldStudentId = $_GET['student_id'] ?? '';
$oldCompanyName = $_GET['company_name'] ?? '';
$oldType = $_GET['type'] ?? '';
$oldStartDate = $_GET['start_date'] ?? '';
$oldEndDate = $_GET['end_date'] ?? '';
$oldTechStack = $_GET['tech_stack'] ?? '';

if ($view === 'update') {
    $id = $_GET['id'] ?? '';
    if (!empty($id)) {
        $internshipToEdit = $internshipDao->getInternshipById($id);
        if (!$internshipToEdit) {
            $errors[] = 'Stage introuvable.';
            $view = '';
        }
    } else {
        $errors[] = 'ID de stage non fourni.';
        $view = '';
    }
}

$formMode = $view === 'update' ? 'update' : 'create';
$formTitle = $view === 'update' ? 'Modifier le stage' : 'Ajouter un stage';
$submitLabel = $view === 'update' ? 'Mettre à jour' : 'Créer';
$studentId = $internshipToEdit ? $internshipToEdit->getStudentId() : $oldStudentId;
$companyName = $internshipToEdit ? $internshipToEdit->getCompanyName() : $oldCompanyName;
$type = $internshipToEdit ? $internshipToEdit->getType() : $oldType;
$startDate = $internshipToEdit ? $internshipToEdit->getStartDate() : $oldStartDate;
$endDate = $internshipToEdit ? $internshipToEdit->getEndDate() : $oldEndDate;
$techStack = $internshipToEdit ? $internshipToEdit->getTechStack() : $oldTechStack;
?>
    <!-- Contenu Stages -->
    <?php if ($view === 'create' || $view === 'update'): ?>
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark"><?= $formTitle; ?></h5>
                <a href="?page=internships" class="btn btn-sm btn-light border">Retour à la liste</a>
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

            <form method="POST" action="../controlle/internshipcontroller.php?action=<?= $formMode; ?>">
                <?php if ($view === 'update'): ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($internshipToEdit->getId(), ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="student_id" class="form-label">Étudiant</label>
                    <select id="student_id" name="student_id" class="form-select" required>
                        <option value="">Sélectionner un étudiant</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?= htmlspecialchars($student->getId(), ENT_QUOTES, 'UTF-8'); ?>" <?= $studentId == $student->getId() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($student->getLastName() . ' ' . $student->getFirstName(), ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="company_name" class="form-label">Entreprise d'accueil</label>
                    <input id="company_name" name="company_name" type="text" class="form-control" required value="<?= htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">Type de stage</label>
                    <select id="type" name="type" class="form-select" required>
                        <option value="">Sélectionner un type</option>
                        <option value="Stage d'initiation" <?= $type == 'Stage d\'initiation' ? 'selected' : '' ?>>Stage d'initiation</option>
                        <option value="Stage technique" <?= $type == 'Stage technique' ? 'selected' : '' ?>>Stage technique</option>
                        <option value="Projet de Fin d'Études (PFE)" <?= $type == 'Projet de Fin d\'Études (PFE)' ? 'selected' : '' ?>>Projet de Fin d'Études (PFE)</option>
                        <option value="Autre" <?= $type == 'Autre' ? 'selected' : '' ?>>Autre</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Date de début</label>
                        <input id="start_date" name="start_date" type="date" class="form-control" required value="<?= htmlspecialchars($startDate, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">Date de fin</label>
                        <input id="end_date" name="end_date" type="date" class="form-control" required value="<?= htmlspecialchars($endDate, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="tech_stack" class="form-label">Technologies utilisées</label>
                    <input id="tech_stack" name="tech_stack" type="text" class="form-control" value="<?= htmlspecialchars($techStack, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <button type="submit" class="btn btn-primary"><?= $submitLabel; ?></button>
            </form>
        </div>
    <?php else: ?>
        <!-- Sous-navigation -->
        <div class="d-flex gap-2 nav-pills-custom mb-4">
            <a href="?page=internships" class="nav-link active">Liste des stages</a>
            <a href="?page=internships&view=create" class="nav-link">Ajouter un stage</a>
        </div>

        <!-- Conteneur Principal -->
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark">Gestion des stages</h5>
                <button class="btn btn-sm btn-light border"><i class="bi bi-chevron-down"></i></button>
            </div>

            <!-- Barre d'actions -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted small fw-medium">
                    (<?= count($internships); ?>) Enregistrements trouvés
                </div>
                <div class="d-flex gap-2">
                    <input type="text" id="internshipSearch" class="form-control form-control-sm" placeholder="Rechercher..." style="width: 200px;">
                    <button class="btn btn-sm btn-custom-filter"><i class="bi bi-funnel me-1"></i> Filtrer</button>
                    <a href="?page=internships&view=create" class="btn btn-sm btn-custom-add"><i class="bi bi-plus-lg me-1"></i> Ajouter</a>
                </div>
            </div>

            <!-- Tableau de données -->
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="internshipTable">
                    <thead class="table-light text-secondary small">
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" class="form-check-input"></th>
                            <th>ID</th>
                            <th>Étudiant</th>
                            <th>Entreprise</th>
                            <th>Type</th>
                            <th>Période</th>
                            <th>Technologies</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                     <tbody>
                        <?php if (empty($internships)) : ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-emoji-neutral fs-3 d-block mb-2"></i> Aucun stage trouvé dans la base de données.
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($internships as $internship) : ?>
                                <tr>
                                    <td><input type="checkbox" class="form-check-input"></td>
                                    <td class="fw-bold">#<?= htmlspecialchars($internship->getId(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($internship->getStudentName(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><i class="bi bi-building me-1"></i><?= htmlspecialchars($internship->getCompanyName(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><span class="badge bg-primary"><?= htmlspecialchars($internship->getType(), ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <td>
                                        <small>
                                            Du <?= htmlspecialchars($internship->getStartDate(), ENT_QUOTES, 'UTF-8'); ?><br>
                                            Au <?= htmlspecialchars($internship->getEndDate(), ENT_QUOTES, 'UTF-8'); ?>
                                        </small>
                                    </td>
                                    <td><?= htmlspecialchars($internship->getTechStack(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="?page=internships&view=update&id=<?= $internship->getId(); ?>" class="btn btn-link text-secondary p-1" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="../controlle/internshipcontroller.php?action=delete&id=<?= $internship->getId(); ?>" class="btn btn-link text-danger p-1" title="Supprimer" onclick="return confirm('Supprimer ce stage ?');">
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
    const searchInput = document.getElementById('internshipSearch');
    const internshipTable = document.getElementById('internshipTable');

    if (searchInput && internshipTable) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = internshipTable.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
});
</script>