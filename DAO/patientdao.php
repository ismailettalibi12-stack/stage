<?php
require_once __DIR__ . '/../model/patient.php';
require_once __DIR__ . '/../config/Database.php';

class patientdao {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function getAllPatients() {
        try {
            $stmt = $this->conn->query("SELECT * FROM patients ORDER BY id DESC");
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
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM patients");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getPatientById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM patients WHERE id = ?");
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
        try {
            $stmt = $this->conn->prepare("INSERT INTO patients (user_id, full_name, birth_date, phone, email, created_at) VALUES (?, ?, ?, ?, ?, ?)");
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
        try {
            $stmt = $this->conn->prepare("UPDATE patients SET full_name = ?, birth_date = ?, phone = ?, email = ? WHERE id = ?");
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
        try {
            $stmt = $this->conn->prepare("DELETE FROM patients WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}

