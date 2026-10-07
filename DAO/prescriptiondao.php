<?php
require_once __DIR__ . '/../model/prescription.php';
require_once __DIR__ . '/../config/Database.php';

class prescriptiondao {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function getAllPrescriptions() {
        try {
            $stmt = $this->conn->query("SELECT * FROM prescriptions ORDER BY id DESC");
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
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM prescriptions");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getPrescriptionById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM prescriptions WHERE id = ?");
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
        try {
            $stmt = $this->conn->prepare("INSERT INTO prescriptions (patient_id, medication_name, dosage, frequency, start_date, end_date, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
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
        try {
            $stmt = $this->conn->prepare("UPDATE prescriptions SET patient_id = ?, medication_name = ?, dosage = ?, frequency = ?, start_date = ?, end_date = ?, notes = ? WHERE id = ?");
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
        try {
            $stmt = $this->conn->prepare("DELETE FROM prescriptions WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}

