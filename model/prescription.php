<?php
class prescription {
    private $id;
    private $patient_id;
    private $medication_name;
    private $dosage;
    private $frequency;
    private $start_date;
    private $end_date;
    private $notes;
    private $created_at;

    public function __construct($id, $patient_id, $medication_name, $dosage, $frequency, $start_date, $end_date, $notes, $created_at) {
        $this->id = $id;
        $this->patient_id = $patient_id;
        $this->medication_name = $medication_name;
        $this->dosage = $dosage;
        $this->frequency = $frequency;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->notes = $notes;
        $this->created_at = $created_at;
    }

    public function getId() {
        return $this->id; 
    }
    
    public function getPatientId() {
        return $this->patient_id;
    }

    public function getMedicationName() {
        return $this->medication_name;
    }

    public function getDosage() {
        return $this->dosage;
    }

    public function getFrequency() {
        return $this->frequency;
    }

    public function getStartDate() {
        return $this->start_date;
    }

    public function getEndDate() {
        return $this->end_date;
    }

    public function getNotes() {
        return $this->notes;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
}
