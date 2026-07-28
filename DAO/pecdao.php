<?php
require_once __DIR__ . '/../model/pec.php';
require_once __DIR__ . '/patientdao.php';

class pecdao {
    private $conn;

    public function __construct() {
        try {
            $this->conn = new PDO('mysql:host=localhost;dbname=mvc_gestion', 'root', '');
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }

    // Récupère tous les patients inscrits pour alimenter le select.
    public function getAllPatients() {
        $stmt = $this->conn->query('SELECT id, full_name FROM patients ORDER BY full_name');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère la liste des demandes PEC avec le nom du patient.
    public function getAllPecRequests() {
        $sql = 'SELECT
                    `id`,
                    `Patient` AS patient_name,
                    `Date de prise en charge` AS date_pec,
                    `Organisme` AS organisme,
                    `Image` AS avatar,
                    `statut` AS statut
                FROM `prc`';

        $stmt = $this->conn->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            return array_merge($row, [
                'statut' => $row['statut'] ?? 'En attente',
            ]);
        }, $rows);
    }

    // Insère une nouvelle demande PEC dans la table prc.
    public function createPecRequest($patientId, $datePec, $organisme, $statut) {
        // Récupérer le nom du patient à partir de l'ID
        $stmt = $this->conn->prepare('SELECT full_name FROM patients WHERE id = ?');
        $stmt->execute([$patientId]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$patient) {
            return false;
        }
        
        $patientName = $patient['full_name'];
        
        $stmt = $this->conn->prepare('INSERT INTO `prc` (`Patient`, `Date de prise en charge`, `Organisme`, `Image`, `statut`) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([
            $patientName,
            $datePec,
            $organisme,
            'image',
            $statut,
        ]);
    }

    // Met à jour une demande PEC existante.
    public function updatePecRequest($id, $patientId, $datePec, $organisme, $statut) {
        // Récupérer le nom du patient à partir de l'ID
        $stmt = $this->conn->prepare('SELECT full_name FROM patients WHERE id = ?');
        $stmt->execute([$patientId]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$patient) {
            return false;
        }
        
        $patientName = $patient['full_name'];
        
        $stmt = $this->conn->prepare('UPDATE `prc` SET `Patient` = ?, `Date de prise en charge` = ?, `Organisme` = ?, `statut` = ? WHERE id = ?');
        return $stmt->execute([
            $patientName,
            $datePec,
            $organisme,
            $statut,
            $id,
        ]);
    }

    // Supprime une demande PEC.
    public function deletePecRequest($id) {
        $stmt = $this->conn->prepare('DELETE FROM `prc` WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // Récupère une demande PEC par son ID.
    public function getPecById($id) {
        $stmt = $this->conn->prepare('SELECT * FROM `prc` WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Compte le total des demandes PEC.
    public function countPecRequests() {
        $stmt = $this->conn->query('SELECT COUNT(*) FROM `prc`');
        return (int) $stmt->fetchColumn();
    }

    // Compte les demandes PEC en attente.
    public function countPendingPecRequests() {
        $stmt = $this->conn->query('SELECT COUNT(*) FROM `prc` WHERE `statut` = "En attente"');
        return (int) $stmt->fetchColumn();
    }
}
