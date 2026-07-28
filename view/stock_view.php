<?php
require_once __DIR__ . '/../DAO/stockdao.php';

$stockDao = new stockdao();
$stockItems = $stockDao->getAllStock();
$view = $_GET['view'] ?? '';
$stockToEdit = null;
$errors = [];
$errorMessage = $_GET['error'] ?? '';
$oldItemName = $_GET['item_name'] ?? '';
$oldCategory = $_GET['category'] ?? '';
$oldQuantity = $_GET['quantity'] ?? '';
$oldUnit = $_GET['unit'] ?? '';
$oldMinQuantity = $_GET['min_quantity'] ?? '';
$oldExpiryDate = $_GET['expiry_date'] ?? '';
$oldSupplier = $_GET['supplier'] ?? '';
$oldUnitPrice = $_GET['unit_price'] ?? '';

if ($view === 'update') {
    $id = $_GET['id'] ?? '';
    if (!empty($id)) {
        $stockToEdit = $stockDao->getStockById($id);
        if (!$stockToEdit) {
            $errors[] = 'Article introuvable.';
            $view = '';
        }
    } else {
        $errors[] = 'ID d\'article non fourni.';
        $view = '';
    }
}

$formMode = $view === 'update' ? 'update' : 'create';
$formTitle = $view === 'update' ? 'Modifier l\'article' : 'Ajouter un article';
$submitLabel = $view === 'update' ? 'Mettre à jour' : 'Créer';
$itemName = $stockToEdit ? $stockToEdit->getItemName() : $oldItemName;
$category = $stockToEdit ? $stockToEdit->getCategory() : $oldCategory;
$quantity = $stockToEdit ? $stockToEdit->getQuantity() : $oldQuantity;
$unit = $stockToEdit ? $stockToEdit->getUnit() : $oldUnit;
$minQuantity = $stockToEdit ? $stockToEdit->getMinQuantity() : $oldMinQuantity;
$expiryDate = $stockToEdit ? $stockToEdit->getExpiryDate() : $oldExpiryDate;
$supplier = $stockToEdit ? $stockToEdit->getSupplier() : $oldSupplier;
$unitPrice = $stockToEdit ? $stockToEdit->getUnitPrice() : $oldUnitPrice;
$createdAt = $stockToEdit ? $stockToEdit->getCreatedAt() : date('Y-m-d H:i:s');
?>
    <!-- Contenu Stock -->
    <?php if ($view === 'create' || $view === 'update'): ?>
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark"><?= $formTitle; ?></h5>
                <a href="?page=stock" class="btn btn-sm btn-light border">Retour à la liste</a>
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

            <form method="POST" action="../controlle/stockcontroller.php?action=<?= $formMode; ?>">
                <?php if ($view === 'update'): ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($stockToEdit->getId(), ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="item_name" class="form-label">Nom de l'article</label>
                    <input id="item_name" name="item_name" type="text" class="form-control" required value="<?= htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Catégorie</label>
                    <select id="category" name="category" class="form-select" required>
                        <option value="">Sélectionner une catégorie</option>
                        <option value="Médicament" <?= $category == 'Médicament' ? 'selected' : '' ?>>Médicament</option>
                        <option value="Matériel" <?= $category == 'Matériel' ? 'selected' : '' ?>>Matériel</option>
                        <option value="Consommable" <?= $category == 'Consommable' ? 'selected' : '' ?>>Consommable</option>
                        <option value="Autre" <?= $category == 'Autre' ? 'selected' : '' ?>>Autre</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="quantity" class="form-label">Quantité</label>
                        <input id="quantity" name="quantity" type="number" class="form-control" required value="<?= htmlspecialchars($quantity, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="unit" class="form-label">Unité</label>
                        <select id="unit" name="unit" class="form-select" required>
                            <option value="">Unité</option>
                            <option value="Unité" <?= $unit == 'Unité' ? 'selected' : '' ?>>Unité</option>
                            <option value="Boîte" <?= $unit == 'Boîte' ? 'selected' : '' ?>>Boîte</option>
                            <option value="Flacon" <?= $unit == 'Flacon' ? 'selected' : '' ?>>Flacon</option>
                            <option value="Litre" <?= $unit == 'Litre' ? 'selected' : '' ?>>Litre</option>
                            <option value="Kg" <?= $unit == 'Kg' ? 'selected' : '' ?>>Kg</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="min_quantity" class="form-label">Quantité minimale</label>
                        <input id="min_quantity" name="min_quantity" type="number" class="form-control" required value="<?= htmlspecialchars($minQuantity, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="expiry_date" class="form-label">Date d'expiration</label>
                        <input id="expiry_date" name="expiry_date" type="date" class="form-control" value="<?= htmlspecialchars($expiryDate, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="supplier" class="form-label">Fournisseur</label>
                        <input id="supplier" name="supplier" type="text" class="form-control" value="<?= htmlspecialchars($supplier, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="unit_price" class="form-label">Prix unitaire (DH)</label>
                        <input id="unit_price" name="unit_price" type="number" step="0.01" class="form-control" value="<?= htmlspecialchars($unitPrice, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><?= $submitLabel; ?></button>
            </form>
        </div>
    <?php else: ?>
        <!-- Sous-navigation -->
        <div class="d-flex gap-2 nav-pills-custom mb-4">
            <a href="?page=stock" class="nav-link active">Liste du stock</a>
            <a href="?page=stock&view=create" class="nav-link">Ajouter un article</a>
        </div>

        <!-- Conteneur Principal -->
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark">Gestion du stock</h5>
                <button class="btn btn-sm btn-light border"><i class="bi bi-chevron-down"></i></button>
            </div>

            <!-- Barre d'actions -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted small fw-medium">
                    (<?= count($stockItems); ?>) Enregistrements trouvés
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-custom-filter"><i class="bi bi-funnel me-1"></i> Filtrer</button>
                    <a href="?page=stock&view=create" class="btn btn-sm btn-custom-add"><i class="bi bi-plus-lg me-1"></i> Ajouter</a>
                </div>
            </div>

            <!-- Tableau de données -->
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="stockTable">
                    <thead class="table-light text-secondary small">
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" class="form-check-input"></th>
                            <th>ID</th>
                            <th>Article</th>
                            <th>Catégorie</th>
                            <th>Quantité</th>
                            <th>Unité</th>
                            <th>Min. Quantité</th>
                            <th>Expiration</th>
                            <th>Fournisseur</th>
                            <th>Prix unitaire</th>
                            <th>Créé le</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                     <tbody>
                        <?php if (empty($stockItems)) : ?>
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">
                                    <i class="bi bi-emoji-neutral fs-3 d-block mb-2"></i> Aucun article trouvé dans le stock.
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($stockItems as $stock) : ?>
                                <?php
                                    $isLowStock = $stock->getQuantity() <= $stock->getMinQuantity();
                                    $rowClass = $isLowStock ? 'table-warning' : '';
                                ?>
                                <tr class="<?= $rowClass ?>">
                                    <td><input type="checkbox" class="form-check-input"></td>
                                    <td class="fw-bold">#<?= htmlspecialchars($stock->getId(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($stock->getItemName(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($stock->getCategory(), ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($stock->getQuantity(), ENT_QUOTES, 'UTF-8'); ?>
                                        <?php if ($isLowStock): ?>
                                            <i class="bi bi-exclamation-triangle-fill text-warning ms-1" title="Stock faible"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($stock->getUnit(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($stock->getMinQuantity(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($stock->getExpiryDate(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($stock->getSupplier(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="fw-bold text-success"><?= number_format($stock->getUnitPrice(), 2) ?> DH</td>
                                    <td class="text-secondary"><?= htmlspecialchars($stock->getCreatedAt(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="?page=stock&view=update&id=<?= $stock->getId(); ?>" class="btn btn-link text-secondary p-1" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="../controlle/stockcontroller.php?action=delete&id=<?= $stock->getId(); ?>" class="btn btn-link text-danger p-1" title="Supprimer" onclick="return confirm('Supprimer cet article ?');">
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
    const searchInput = document.getElementById('stockSearch');
    const stockTable = document.getElementById('stockTable');

    if (searchInput && stockTable) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = stockTable.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
});
</script>
