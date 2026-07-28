<?php
require_once __DIR__ . '/../model/prescription.php';

class prescriptiondao {
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

    public function getAllPrescriptions() {
        $conn = $this->connect();
        if (!$conn) {
            return [];
        }

        try {
            $stmt = $conn->query("SELECT * FROM prescriptions ORDER BY id DESC");
            $rows = $stmt->fetchAll();
            $prescriptions = [];
            foreach ($rows as $row) {
                $prescriptions[] = new prescription($row['id'], $row['patient_id'], $row['medication_name'], $row['dosage'], $row['frequency'], $row['start_date'], $row['end_date'], $row['notes'], $row['created_at']);
            }
            return $prescriptions;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function countPrescriptions() {
        $conn = $this->connect();
        if (!$conn) {
            return 0;
        }

        try {
            $stmt = $conn->query("SELECT COUNT(*) FROM prescriptions");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getPrescriptionById($id) {
        $conn = $this->connect();
        if (!$conn) {
            return null;
        }

        try {
            $stmt = $conn->prepare("SELECT * FROM prescriptions WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row) {
                return new prescription($row['id'], $row['patient_id'], $row['medication_name'], $row['dosage'], $row['frequency'], $row['start_date'], $row['end_date'], $row['notes'], $row['created_at']);
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function addPrescription($prescription) {
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("INSERT INTO prescriptions (patient_id, medication_name, dosage, frequency, start_date, end_date, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $prescription->getPatientId(),
                $prescription->getMedicationName(),
                $prescription->getDosage(),
                $prescription->getFrequency(),
                $prescription->getStartDate(),
                $prescription->getEndDate(),
                $prescription->getNotes(),
                $prescription->getCreatedAt()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updatePrescription($prescription) {
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("UPDATE prescriptions SET patient_id = ?, medication_name = ?, dosage = ?, frequency = ?, start_date = ?, end_date = ?, notes = ? WHERE id = ?");
            return $stmt->execute([
                $prescription->getPatientId(),
                $prescription->getMedicationName(),
                $prescription->getDosage(),
                $prescription->getFrequency(),
                $prescription->getStartDate(),
                $prescription->getEndDate(),
                $prescription->getNotes(),
                $prescription->getId()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deletePrescription($id) {
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("DELETE FROM prescriptions WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
