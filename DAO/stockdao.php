<?php
require_once __DIR__ . '/../model/stock.php';

class stockdao {
    private $conn;

    public function __construct() {
        $this->conn = null;
        $this->connect();
    }

    private function connect() {
        if ($this->conn instanceof PDO) {
            return $this->conn;
        }

        try {
            $this->conn = new PDO("mysql:host=localhost;dbname=mvc_gestion;charset=utf8mb4", "root", "", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            return $this->conn;
        } catch (PDOException $e) {
            $this->conn = null;
            return null;
        }
    }

    public function getAllStock() {
        $conn = $this->connect();
        if (!$conn) {
            return [];
        }

        try {
            $stmt = $conn->query("SELECT * FROM stock ORDER BY id DESC");
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
        $conn = $this->connect();
        if (!$conn) {
            return 0;
        }

        try {
            $stmt = $conn->query("SELECT COUNT(*) FROM stock");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getStockById($id) {
        $conn = $this->connect();
        if (!$conn) {
            return null;
        }

        try {
            $stmt = $conn->prepare("SELECT * FROM stock WHERE id = ?");
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
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("INSERT INTO stock (item_name, category, quantity, unit, min_quantity, expiry_date, supplier, unit_price, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("UPDATE stock SET item_name = ?, category = ?, quantity = ?, unit = ?, min_quantity = ?, expiry_date = ?, supplier = ?, unit_price = ? WHERE id = ?");
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
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("DELETE FROM stock WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getLowStockItems() {
        $conn = $this->connect();
        if (!$conn) {
            return [];
        }

        try {
            $stmt = $conn->query("SELECT * FROM stock WHERE quantity <= min_quantity ORDER BY quantity ASC");
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
        $conn = $this->connect();
        if (!$conn) {
            return 0;
        }

        try {
            $stmt = $conn->query("SELECT COUNT(*) FROM stock WHERE quantity <= min_quantity");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getTotalStockValue() {
        $conn = $this->connect();
        if (!$conn) {
            return 0;
        }

        try {
            $stmt = $conn->query("SELECT SUM(quantity * unit_price) FROM stock");
            $result = $stmt->fetchColumn();
            return $result ? (float) $result : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getStockByCategory() {
        $conn = $this->connect();
        if (!$conn) {
            return [];
        }

        try {
            $stmt = $conn->query("SELECT category, SUM(quantity) as total_quantity, SUM(quantity * unit_price) as total_value FROM stock GROUP BY category");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
