<?php
require_once __DIR__ . '/../model/validation.php';

class validationdao {
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

    public function getAllValidations() {
        $conn = $this->connect();
        if (!$conn) {
            return [];
        }

        $validations = [];
        try {
            $query = "SELECT v.*, 
                             CONCAT(s.last_name, ' ', s.first_name) AS student_name,
                             i.company_name, i.type AS internship_type
                      FROM validations v
                      JOIN internships i ON v.internship_id = i.id
                      JOIN students s ON i.student_id = s.id
                      ORDER BY v.defense_date DESC, v.id DESC";
                      
            $stmt = $conn->query($query);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $validations[] = new validation(
                    $row['id'],
                    $row['internship_id'],
                    $row['defense_date'],
                    $row['jury_members'],
                    $row['final_grade'],
                    $row['status'],
                    $row['created_at'],
                    $row['student_name'],
                    $row['company_name'],
                    $row['internship_type']
                );
            }
        } catch (PDOException $e) {
            error_log("Erreur getAllValidations: " . $e->getMessage());
        }
        return $validations;
    }

    public function getValidationById($id) {
        $conn = $this->connect();
        if (!$conn) {
            return null;
        }

        try {
            $query = "SELECT v.*, 
                             CONCAT(s.last_name, ' ', s.first_name) AS student_name,
                             i.company_name, i.type AS internship_type
                      FROM validations v
                      JOIN internships i ON v.internship_id = i.id
                      JOIN students s ON i.student_id = s.id
                      WHERE v.id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return new Validation(
                    $row['id'],
                    $row['internship_id'],
                    $row['defense_date'],
                    $row['jury_members'],
                    $row['final_grade'],
                    $row['status'],
                    $row['created_at'],
                    $row['student_name'],
                    $row['company_name'],
                    $row['internship_type']
                );
            }
        } catch (PDOException $e) {
            error_log("Erreur getValidationById: " . $e->getMessage());
        }
        return null;
    }

    public function createValidation(Validation $validation) {
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("INSERT INTO validations (internship_id, defense_date, jury_members, final_grade, status) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([
                $validation->getInternshipId(),
                $validation->getDefenseDate() ?: null,
                $validation->getJuryMembers() ?: null,
                $validation->getFinalGrade() !== "" ? $validation->getFinalGrade() : null,
                $validation->getStatus()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur createValidation: " . $e->getMessage());
            return false;
        }
    }

    public function updateValidation(Validation $validation) {
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("UPDATE validations SET internship_id = ?, defense_date = ?, jury_members = ?, final_grade = ?, status = ? WHERE id = ?");
            return $stmt->execute([
                $validation->getInternshipId(),
                $validation->getDefenseDate() ?: null,
                $validation->getJuryMembers() ?: null,
                $validation->getFinalGrade() !== "" ? $validation->getFinalGrade() : null,
                $validation->getStatus(),
                $validation->getId()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur updateValidation: " . $e->getMessage());
            return false;
        }
    }

    public function deleteValidation($id) {
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("DELETE FROM validations WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Erreur deleteValidation: " . $e->getMessage());
            return false;
        }
    }

    public function countValidations() {
        $conn = $this->connect();
        if (!$conn) {
            return 0;
        }

        try {
            $stmt = $conn->query("SELECT COUNT(*) FROM validations");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function countValidationsByStatus($status) {
        $conn = $this->connect();
        if (!$conn) {
            return 0;
        }

        try {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM validations WHERE status = ?");
            $stmt->execute([$status]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getValidationSuccessRate() {
        $conn = $this->connect();
        if (!$conn) {
            return 0;
        }

        try {
            $total = $this->countValidations();
            if ($total === 0) {
                return 0;
            }
            $validated = $this->countValidationsByStatus('Validé');
            return round(($validated / $total) * 100, 1);
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getUpcomingDefenses($days = 7) {
        $conn = $this->connect();
        if (!$conn) {
            return [];
        }

        try {
            $stmt = $conn->prepare("SELECT v.*, 
                                         CONCAT(s.last_name, ' ', s.first_name) AS student_name,
                                         i.company_name
                                  FROM validations v
                                  JOIN internships i ON v.internship_id = i.id
                                  JOIN students s ON i.student_id = s.id
                                  WHERE v.defense_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                                  ORDER BY v.defense_date ASC");
            $stmt->execute([$days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
