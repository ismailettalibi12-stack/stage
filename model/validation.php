<?php
class validation {
    private $id;
    private $internship_id;
    private $defense_date;
    private $jury_members;
    private $final_grade;
    private $status;
    private $created_at;

    // Propriétés virtuelles pour l'affichage (récupérées via jointures SQL)
    private $student_name;
    private $company_name;
    private $internship_type;

    public function __construct($id = null, $internship_id = "", $defense_date = null, $jury_members = null, $final_grade = null, $status = "En cours", $created_at = null, $student_name = "", $company_name = "", $internship_type = "") {
        $this->id = $id;
        $this->internship_id = $internship_id;
        $this->defense_date = $defense_date;
        $this->jury_members = $jury_members;
        $this->final_grade = $final_grade;
        $this->status = $status;
        $this->created_at = $created_at;
        
        $this->student_name = $student_name;
        $this->company_name = $company_name;
        $this->internship_type = $internship_type;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getInternshipId() { return $this->internship_id; }
    public function getDefenseDate() { return $this->defense_date; }
    public function getJuryMembers() { return $this->jury_members; }
    public function getFinalGrade() { return $this->final_grade; }
    public function getStatus() { return $this->status; }
    public function getCreatedAt() { return $this->created_at; }
    
    public function getStudentName() { return $this->student_name; }
    public function getCompanyName() { return $this->company_name; }
    public function getInternshipType() { return $this->internship_type; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setInternshipId($internship_id) { $this->internship_id = $internship_id; }
    public function setDefenseDate($defense_date) { $this->defense_date = $defense_date; }
    public function setJuryMembers($jury_members) { $this->jury_members = $jury_members; }
    public function setFinalGrade($final_grade) { $this->final_grade = $final_grade; }
    public function setStatus($status) { $this->status = $status; }
}
?>