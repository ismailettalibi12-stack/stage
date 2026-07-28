<?php
require_once __DIR__ . '/../DAO/pecdao.php';
require_once __DIR__ . '/../DAO/patientdao.php';

$pecList = $pecList ?? [];
$successMessage = $_GET['success'] ?? '';
$errorMessage = $_GET['error'] ?? '';

$pecDao = new pecdao();
$patientDao = new patientdao();

$view = $_GET['view'] ?? '';
$pecToEdit = null;
$errors = [];
$oldPatientId = $_GET['patient_id'] ?? '';
$oldDatePec = $_GET['date_pec'] ?? '';
$oldOrganisme = $_GET['organisme'] ?? '';
$oldStatut = $_GET['statut'] ?? 'En attente';

if ($view === 'update') {
    $id = $_GET['id'] ?? '';
    if (!empty($id)) {
        $pecToEdit = $pecDao->getPecById($id);
        if (!$pecToEdit) {
            $errors[] = 'Demande PEC introuvable.';
            $view = '';
        }
    } else {
        $errors[] = 'ID de demande PEC non fourni.';
        $view = '';
    }
}

$formMode = $view === 'update' ? 'update' : 'create';
$formTitle = $view === 'update' ? 'Modifier la demande PEC' : 'Nouvelle demande PEC';
$submitLabel = $view === 'update' ? 'Mettre à jour' : 'Créer';
$patientId = $pecToEdit ? $pecToEdit['Patient'] : $oldPatientId;
$datePec = $pecToEdit ? $pecToEdit['Date de prise en charge'] : $oldDatePec;
$organisme = $pecToEdit ? $pecToEdit['Organisme'] : $oldOrganisme;
$statut = $pecToEdit ? $pecToEdit['statut'] : $oldStatut;
?>
<style>
    /* Styles spécifiques pour reproduire le design exact de la maquette PEC */
    
    /* Pilule supérieure "Les demandes de PEC" */
    .pec-top-pill {
        background-color: #fce8e6;
        color: #b02a2a;
        padding: 8px 24px;
        border-radius: 25px;
        display: inline-block;
        font-weight: 500;
        margin-bottom: 24px;
        font-size: 0.95rem;
    }

    /* Boutons d'action */
    .btn-outline-custom {
        border: 1px solid #b02a2a;
        color: #b02a2a;
        background-color: white;
    }
    .btn-outline-custom:hover { background-color: #fce8e6; color: #b02a2a; }
    .btn-fill-custom {
        background-color: #fce8e6;
        color: #b02a2a;
        border: none;
    }
    .btn-fill-custom:hover { background-color: #fadad7; color: #b02a2a; }
    
    /* Le petit séparateur jaune */
    .yellow-divider {
        width: 5px;
        height: 24px;
        background-color: #f6e05e;
        border-radius: 4px;
        display: inline-block;
    }

    /* Le tableau séparé façon "Cartes" */
    .table-pec {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px; /* L'espace magique entre les lignes */
    }
    
    /* En-tête du tableau */
    .table-pec thead th {
        background-color: #eef2f6;
        color: #7a8b9a;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 14px 16px;
        border: none;
    }
    .table-pec thead th:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .table-pec thead th:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

    /* Lignes du tableau */
    .table-pec tbody tr {
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .table-pec tbody td {
        background: #ffffff;
        padding: 16px;
        vertical-align: middle;
        border-top: 1px solid #f0f0f0;
        border-bottom: 1px solid #f0f0f0;
        color: #333;
        font-size: 0.95rem;
    }
    .table-pec tbody td:first-child { 
        border-left: 1px solid #f0f0f0; 
        border-top-left-radius: 12px; 
        border-bottom-left-radius: 12px; 
    }
    .table-pec tbody td:last-child { 
        border-right: 1px solid #f0f0f0; 
        border-top-right-radius: 12px; 
        border-bottom-right-radius: 12px; 
    }

    /* Badges et Icônes */
    .badge-attente {
        background-color: #fef0e6;
        color: #d97706;
        padding: 6px 16px;
        font-weight: 500;
    }
    .action-icon {
        color: #a0aec0;
        text-decoration: none;
        font-size: 1.2rem;
        margin-left: 8px;
    }
    .action-icon:hover { color: #b02a2a; }

    /* Avatar généré */
    .avatar-initial {
        width: 38px;
        height: 38px;
        background-color: #e0e7ff;
        color: #4f46e5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    .avatar-img {
        width: 38px;
        height: 38px;
        object-fit: cover;
        border-radius: 50%;
    }
</style>

<!-- 1. Onglet supérieur -->
<div class="pec-top-pill">
    Les demandes de PEC
</div>

<!-- Messages de succès ou d'erreur -->
<?php if (!empty($successMessage)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($errorMessage)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($view === 'create' || $view === 'update'): ?>
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0 text-dark"><?= $formTitle; ?></h5>
            <a href="?page=pec" class="btn btn-sm btn-light border">Retour à la liste</a>
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

        <form method="POST" action="../controlle/peccontroller.php?action=<?= $formMode; ?>">
            <?php if ($view === 'update'): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="patient_id" class="form-label">Patient</label>
                <select id="patient_id" name="patient_id" class="form-select" required>
                    <option value="">Sélectionner un patient</option>
                    <?php foreach ($pecDao->getAllPatients() as $patient): ?>
                        <option value="<?= htmlspecialchars($patient['id'], ENT_QUOTES, 'UTF-8'); ?>" <?= $patientId == $patient['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($patient['full_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="date_pec" class="form-label">Date de prise en charge</label>
                <input id="date_pec" name="date_pec" type="date" class="form-control" required value="<?= htmlspecialchars($datePec, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="mb-3">
                <label for="organisme" class="form-label">Organisme</label>
                <input id="organisme" name="organisme" type="text" class="form-control" required value="<?= htmlspecialchars($organisme, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="mb-3">
                <label for="statut" class="form-label">Statut</label>
                <select id="statut" name="statut" class="form-select" required>
                    <option value="En attente" <?= $statut == 'En attente' ? 'selected' : '' ?>>En attente</option>
                    <option value="Validé" <?= $statut == 'Validé' ? 'selected' : '' ?>>Validé</option>
                    <option value="Rejeté" <?= $statut == 'Rejeté' ? 'selected' : '' ?>>Rejeté</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><?= $submitLabel; ?></button>
        </form>
    </div>
<?php else: ?>
<!-- 2. Carte principale -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background-color: #ffffff;">
    <h5 class="fw-bold mb-3 text-dark">Gestion pec</h5>
    
    <!-- Ligne de séparation très fine -->
    <hr style="border-top: 1px solid #eaeaea; opacity: 1; margin-bottom: 1.5rem;">

    <!-- Barre d'actions supérieurs -->
    <div class="d-flex justify-content-end align-items-center gap-3 mb-2">
        <button class="btn btn-outline-custom rounded-pill px-4 py-2 fw-medium">
            <i class="bi bi-funnel me-1"></i> Filtrer
        </button>

         

        <!-- Bouton déclencheur propre du modal Bootstrap -->
        <button type="button" class="btn btn-fill-custom rounded-pill px-4 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#addPecModal">
            + Ajouter demande PEC
        </button>
    </div>

    <!-- Nombre d'enregistrements -->
    <div class="text-secondary mb-2 mt-2" style="font-size: 0.95rem;">
        (<?= count($pecList); ?>) Enregistrements trouvés
    </div>

    <!-- Tableau des données stylisé -->
    <div class="table-responsive">
        <table class="table-pec" id="pecTable">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;"><input type="checkbox" class="form-check-input"></th>
                    <th style="width: 80px;">Image</th>
                    <th>Patient</th>
                    <th>Date de prise en charge</th>
                    <th>Organisme</th>
                    <th>Statut</th>
                    <th class="text-center" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pecList)) : ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Aucune demande de prise en charge trouvée.
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($pecList as $pec): ?>
                        <tr>
                            <td class="text-center"><input type="checkbox" class="form-check-input"></td>
                            <td>
                                <?php if (!empty($pec['avatar']) && file_exists($pec['avatar'])): ?>
                                    <img src="<?= htmlspecialchars($pec['avatar'], ENT_QUOTES, 'UTF-8'); ?>" alt="Avatar" class="avatar-img">
                                <?php else: ?>
                                    <?php
                                        $name = trim($pec['patient_name'] ?? 'Patient');
                                        $words = explode(' ', $name);
                                        $initials = mb_strtoupper(mb_substr($words[0], 0, 1) . (isset($words[1]) ? mb_substr($words[1], 0, 1) : ''));
                                    ?>
                                    <div class="avatar-initial">
                                        <?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="fw-medium text-dark"><?= htmlspecialchars($pec['patient_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($pec['date_pec'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($pec['organisme'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <span class="badge rounded-pill badge-attente">
                                    <i class="bi bi-clock me-1"></i> <?= htmlspecialchars($pec['statut'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="?page=pec&view=update&id=<?= $pec['id']; ?>" class="btn btn-link text-secondary p-1" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="../controlle/peccontroller.php?action=delete&id=<?= $pec['id']; ?>" class="btn btn-link text-danger p-1" title="Supprimer" onclick="return confirm('Supprimer cette demande PEC ?');">
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
    const searchInput = document.getElementById('pecSearch');
    const pecTable = document.getElementById('pecTable');

    if (searchInput && pecTable) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = pecTable.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
});
</script>

<!-- Modal pour ajouter une demande PEC -->
<div class="modal fade" id="addPecModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle demande de PEC</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../controlle/peccontroller.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label for="patient_id" class="form-label">Patient</label>
                        <select id="patient_id" name="patient_id" class="form-select" required>
                            <option value="">Sélectionner un patient...</option>
                            <?php 
                            $patients = $pecDao->getAllPatients();
                            foreach ($patients as $patient): 
                            ?>
                                <option value="<?= htmlspecialchars($patient['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?= htmlspecialchars($patient['full_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="date_pec" class="form-label">Date de prise en charge</label>
                        <input id="date_pec" name="date_pec" type="date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="organisme" class="form-label">Organisme</label>
                        <input id="organisme" name="organisme" type="text" class="form-control" placeholder="Ex: CNOPS, RAMED" required>
                    </div>

                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select id="statut" name="statut" class="form-select" required>
                            <option value="En attente" selected>En attente</option>
                            <option value="Validé">Validé</option>
                            <option value="Rejeté">Rejeté</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-fill-custom rounded-pill px-4">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>