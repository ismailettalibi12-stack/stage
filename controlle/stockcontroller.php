<?php
require_once __DIR__ . '/../DAO/stockdao.php';
require_once __DIR__ . '/../model/stock.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function redirectToDashboard(array $params = []): void {
    $query = http_build_query($params);
    $location = '../view/dashbord.php' . ($query !== '' ? '?' . $query : '');
    header('Location: ' . $location);
    exit;
}

$dao = new stockdao();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $id = $_POST['id'] ?? '';
        $item_name = trim($_POST['item_name'] ?? '');
        $category = $_POST['category'] ?? '';
        $quantity = $_POST['quantity'] ?? '';
        $unit = $_POST['unit'] ?? '';
        $min_quantity = $_POST['min_quantity'] ?? '';
        $expiry_date = $_POST['expiry_date'] ?? '';
        $supplier = trim($_POST['supplier'] ?? '');
        $unit_price = $_POST['unit_price'] ?? '0.00';
        $created_at = date('Y-m-d H:i:s');

        if (empty($item_name) || empty($category) || empty($quantity) || empty($unit) || empty($min_quantity)) {
            redirectToDashboard([
                'page' => 'stock',
                'view' => 'create',
                'error' => 'Veuillez remplir tous les champs obligatoires.',
                'item_name' => $item_name,
                'category' => $category,
                'quantity' => $quantity,
                'unit' => $unit,
                'min_quantity' => $min_quantity,
                'expiry_date' => $expiry_date,
                'supplier' => $supplier,
                'unit_price' => $unit_price,
            ]);
        }

        if (!is_numeric($quantity) || !is_numeric($min_quantity) || !is_numeric($unit_price)) {
            redirectToDashboard([
                'page' => 'stock',
                'view' => 'create',
                'error' => 'La quantité, la quantité minimale et le prix unitaire doivent être des nombres.',
                'item_name' => $item_name,
                'category' => $category,
                'quantity' => $quantity,
                'unit' => $unit,
                'min_quantity' => $min_quantity,
                'expiry_date' => $expiry_date,
                'supplier' => $supplier,
                'unit_price' => $unit_price,
            ]);
        }

        $quantity = (int)$quantity;
        $min_quantity = (int)$min_quantity;
        $unit_price = (float)$unit_price;

        $stock = new stock($id, $item_name, $category, $quantity, $unit, $min_quantity, $expiry_date, $supplier, $unit_price, $created_at, null);
        if ($dao->addStock($stock)) {
            redirectToDashboard(['page' => 'stock']);
        }
        redirectToDashboard(['page' => 'stock', 'error' => "Impossible d'ajouter l'article."]);
        break;

    case 'update':
        $id = $_POST['id'] ?? '';
        $item_name = trim($_POST['item_name'] ?? '');
        $category = $_POST['category'] ?? '';
        $quantity = $_POST['quantity'] ?? '';
        $unit = $_POST['unit'] ?? '';
        $min_quantity = $_POST['min_quantity'] ?? '';
        $expiry_date = $_POST['expiry_date'] ?? '';
        $supplier = trim($_POST['supplier'] ?? '');
        $unit_price = $_POST['unit_price'] ?? '0.00';

        if (empty($item_name) || empty($category) || empty($quantity) || empty($unit) || empty($min_quantity)) {
            redirectToDashboard([
                'page' => 'stock',
                'view' => 'update',
                'id' => $id,
                'error' => 'Veuillez remplir tous les champs obligatoires.',
                'item_name' => $item_name,
                'category' => $category,
                'quantity' => $quantity,
                'unit' => $unit,
                'min_quantity' => $min_quantity,
                'expiry_date' => $expiry_date,
                'supplier' => $supplier,
                'unit_price' => $unit_price,
            ]);
        }

        if (!is_numeric($quantity) || !is_numeric($min_quantity) || !is_numeric($unit_price)) {
            redirectToDashboard([
                'page' => 'stock',
                'view' => 'update',
                'id' => $id,
                'error' => 'La quantité, la quantité minimale et le prix unitaire doivent être des nombres.',
                'item_name' => $item_name,
                'category' => $category,
                'quantity' => $quantity,
                'unit' => $unit,
                'min_quantity' => $min_quantity,
                'expiry_date' => $expiry_date,
                'supplier' => $supplier,
                'unit_price' => $unit_price,
            ]);
        }

        $quantity = (int)$quantity;
        $min_quantity = (int)$min_quantity;
        $unit_price = (float)$unit_price;

        $stock = new stock($id, $item_name, $category, $quantity, $unit, $min_quantity, $expiry_date, $supplier, $unit_price, null, null);
        if ($dao->updateStock($stock)) {
            redirectToDashboard(['page' => 'stock']);
        }
        redirectToDashboard(['page' => 'stock', 'error' => "Impossible de mettre à jour l'article."]);
        break;

    case 'delete':
        $id = $_GET['id'] ?? '';
        if (!empty($id)) {
            $dao->deleteStock($id);
        }
        redirectToDashboard(['page' => 'stock']);
        break;
}

redirectToDashboard(['page' => 'stock']);
?>
