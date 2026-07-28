<?php
require_once __DIR__ . '/../model/patient.php';

class patientdao {
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

    public function getAllPatients() {
        $conn = $this->connect();
        if (!$conn) {
            return [];
        }

        try {
            $stmt = $conn->query("SELECT * FROM patients ORDER BY id DESC");
            $rows = $stmt->fetchAll();
            $patients = [];
            foreach ($rows as $row) {
                $patients[] = new patient($row['id'], $row['user_id'], $row['full_name'], $row['birth_date'], $row['phone'], $row['email'], $row['created_at']);
            }
            return $patients;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function countPatients() {
        $conn = $this->connect();
        if (!$conn) {
            return 0;
        }

        try {
            $stmt = $conn->query("SELECT COUNT(*) FROM patients");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getPatientById($id) {
        $conn = $this->connect();
        if (!$conn) {
            return null;
        }

        try {
            $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row) {
                return new patient($row['id'], $row['user_id'], $row['full_name'], $row['birth_date'], $row['phone'], $row['email'], $row['created_at']);
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function addPatient($patient) {
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("INSERT INTO patients (user_id, full_name, birth_date, phone, email, created_at) VALUES (?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $patient->getUserId(),
                $patient->getFullName(),
                $patient->getBirthDate(),
                $patient->getPhone(),
                $patient->getEmail(),
                $patient->getCreatedAt()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updatePatient($patient) {
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("UPDATE patients SET full_name = ?, birth_date = ?, phone = ?, email = ? WHERE id = ?");
            return $stmt->execute([
                $patient->getFullName(),
                $patient->getBirthDate(),
                $patient->getPhone(),
                $patient->getEmail(),
                $patient->getId()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deletePatient($id) {
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
