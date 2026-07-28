<?php
class pec {
    private $id;
    private $patient_id;
    private $date_pec;
    private $organisme;
    private $statut;
    private $created_at;

    public function __construct($id, $patient_id, $date_pec, $organisme, $statut, $created_at) {
        $this->id = $id;
        $this->patient_id = $patient_id;
        $this->date_pec = $date_pec;
        $this->organisme = $organisme;
        $this->statut = $statut;
        $this->created_at = $created_at;
    }

    public function getId() {
        return $this->id;
    }

    public function getPatientId() {
        return $this->patient_id;
    }

    public function getDatePec() {
        return $this->date_pec;
    }

    public function getOrganisme() {
        return $this->organisme;
    }

    public function getStatut() {
        return $this->statut;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
}
