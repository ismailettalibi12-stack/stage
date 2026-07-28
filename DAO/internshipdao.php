<?php
require_once __DIR__ . '/../model/internship.php';

class internshipdao {
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

    public function getAllInternships() {
        $conn = $this->connect();
        if (!$conn) {
            return [];
        }

        $internships = [];
        try {
            $query = "SELECT i.*, CONCAT(s.last_name, ' ', s.first_name) AS student_name 
                      FROM internships i 
                      JOIN students s ON i.student_id = s.id 
                      ORDER BY i.start_date DESC";
                      
            $stmt = $conn->query($query);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $internships[] = new Internship(
                    $row['id'],
                    $row['student_id'],
                    $row['company_name'],
                    $row['type'],
                    $row['start_date'],
                    $row['end_date'],
                    $row['tech_stack'],
                    $row['created_at'],
                    $row['student_name']
                );
            }
        } catch (PDOException $e) {
            error_log("Erreur getAllInternships: " . $e->getMessage());
        }
        return $internships;
    }

    public function getInternshipById($id) {
        $conn = $this->connect();
        if (!$conn) {
            return null;
        }

        try {
            $query = "SELECT i.*, CONCAT(s.last_name, ' ', s.first_name) AS student_name 
                      FROM internships i 
                      JOIN students s ON i.student_id = s.id 
                      WHERE i.id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return new internship(
                    $row['id'],
                    $row['student_id'],
                    $row['company_name'],
                    $row['type'],
                    $row['start_date'],
                    $row['end_date'],
                    $row['tech_stack'],
                    $row['created_at'],
                    $row['student_name']
                );
            }
        } catch (PDOException $e) {
            error_log("Erreur getInternshipById: " . $e->getMessage());
        }
        return null;
    }

    public function createInternship(Internship $internship) {
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("INSERT INTO internships (student_id, company_name, type, start_date, end_date, tech_stack) VALUES (?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $internship->getStudentId(),
                $internship->getCompanyName(),
                $internship->getType(),
                $internship->getStartDate(),
                $internship->getEndDate(),
                $internship->getTechStack()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur createInternship: " . $e->getMessage());
            return false;
        }
    }

    public function updateInternship(Internship $internship) {
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("UPDATE internships SET student_id = ?, company_name = ?, type = ?, start_date = ?, end_date = ?, tech_stack = ? WHERE id = ?");
            return $stmt->execute([
                $internship->getStudentId(),
                $internship->getCompanyName(),
                $internship->getType(),
                $internship->getStartDate(),
                $internship->getEndDate(),
                $internship->getTechStack(),
                $internship->getId()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur updateInternship: " . $e->getMessage());
            return false;
        }
    }

    public function deleteInternship($id) {
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("DELETE FROM internships WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Erreur deleteInternship: " . $e->getMessage());
            return false;
        }
    }

    public function countInternships() {
        $conn = $this->connect();
        if (!$conn) {
            return 0;
        }

        try {
            $stmt = $conn->query("SELECT COUNT(*) FROM internships");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function countActiveInternships() {
        $conn = $this->connect();
        if (!$conn) {
            return 0;
        }

        try {
            $stmt = $conn->query("SELECT COUNT(*) FROM internships WHERE end_date >= CURDATE()");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
}
