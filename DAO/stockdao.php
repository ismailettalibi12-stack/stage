<?php
require_once __DIR__ . '/../model/stock.php';
require_once __DIR__ . '/../config/Database.php';

class stockdao {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function getAllStock() {
        try {
            $stmt = $this->conn->query("SELECT * FROM stock ORDER BY id DESC");
            $rows = $stmt->fetchAll();
            $stockItems = [];
            foreach ($rows as $row) {
                $stockItems[] = new stock(
                    $row['id'], 
                    $row['item_name'], 
                    $row['category'], 
                    (int)$row['quantity'], 
                    $row['unit'], 
                    (int)$row['min_quantity'], 
                    $row['expiry_date'] ?? null, 
                    $row['supplier'] ?? null, 
                    (float)($row['unit_price'] ?? 0),
                    $row['created_at'],
                    $row['updated_at'] ?? null
                );
            }
            return $stockItems;
        } catch (PDOException $e) {
            error_log("Stock DAO getAllStock Error: " . $e->getMessage());
            return [];
        }
    }

    public function countStock() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM stock");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getStockById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM stock WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row) {
                return new stock($row['id'], $row['item_name'], $row['category'], $row['quantity'], $row['unit'], $row['min_quantity'], $row['expiry_date'], $row['supplier'], (float)($row['unit_price'] ?? 0), $row['created_at'], $row['updated_at'] ?? null);
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function addStock($stock) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO stock (item_name, category, quantity, unit, min_quantity, expiry_date, supplier, unit_price, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $stock->getItemName(),
                $stock->getCategory(),
                $stock->getQuantity(),
                $stock->getUnit(),
                $stock->getMinQuantity(),
                $stock->getExpiryDate(),
                $stock->getSupplier(),
                $stock->getUnitPrice(),
                $stock->getCreatedAt()
            ]);
        } catch (PDOException $e) {
            error_log("Stock DAO Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateStock($stock) {
        try {
            $stmt = $this->conn->prepare("UPDATE stock SET item_name = ?, category = ?, quantity = ?, unit = ?, min_quantity = ?, expiry_date = ?, supplier = ?, unit_price = ? WHERE id = ?");
            return $stmt->execute([
                $stock->getItemName(),
                $stock->getCategory(),
                $stock->getQuantity(),
                $stock->getUnit(),
                $stock->getMinQuantity(),
                $stock->getExpiryDate(),
                $stock->getSupplier(),
                $stock->getUnitPrice(),
                $stock->getId()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteStock($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM stock WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getLowStockItems() {
        try {
            $stmt = $this->conn->query("SELECT * FROM stock WHERE quantity <= min_quantity ORDER BY quantity ASC");
            $rows = $stmt->fetchAll();
            $stockItems = [];
            foreach ($rows as $row) {
                $stockItems[] = new stock(
                    $row['id'], 
                    $row['item_name'], 
                    $row['category'], 
                    (int)$row['quantity'], 
                    $row['unit'], 
                    (int)$row['min_quantity'], 
                    $row['expiry_date'] ?? null, 
                    $row['supplier'] ?? null, 
                    (float)($row['unit_price'] ?? 0),
                    $row['created_at'],
                    $row['updated_at'] ?? null
                );
            }
            return $stockItems;
        } catch (PDOException $e) {
            error_log("Stock DAO getLowStockItems Error: " . $e->getMessage());
            return [];
        }
    }

    public function countLowStock() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM stock WHERE quantity <= min_quantity");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getTotalStockValue() {
        try {
            $stmt = $this->conn->query("SELECT SUM(quantity * unit_price) FROM stock");
            $result = $stmt->fetchColumn();
            return $result ? (float) $result : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getStockByCategory() {
        try {
            $stmt = $this->conn->query("SELECT category, SUM(quantity) as total_quantity, SUM(quantity * unit_price) as total_value FROM stock GROUP BY category");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

