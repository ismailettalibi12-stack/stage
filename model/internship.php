<?php
class internship {
    private $id;
    private $student_id;
    private $company_name;
    private $type;
    private $start_date;
    private $end_date;
    private $tech_stack;
    private $created_at;
    private $student_name;

    public function __construct($id = null, $student_id = "", $company_name = "", $type = "", $start_date = "", $end_date = "", $tech_stack = "", $created_at = null, $student_name = "") {
        $this->id = $id;
        $this->student_id = $student_id;
        $this->company_name = $company_name;
        $this->type = $type;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->tech_stack = $tech_stack;
        $this->created_at = $created_at;
        $this->student_name = $student_name;
    }

    public function getId() { return $this->id; }
    public function getStudentId() { return $this->student_id; }
    public function getCompanyName() { return $this->company_name; }
    public function getType() { return $this->type; }
    public function getStartDate() { return $this->start_date; }
    public function getEndDate() { return $this->end_date; }
    public function getTechStack() { return $this->tech_stack; }
    public function getCreatedAt() { return $this->created_at; }
    public function getStudentName() { return $this->student_name; }

    public function setId($id) { $this->id = $id; }
    public function setStudentId($student_id) { $this->student_id = $student_id; }
    public function setCompanyName($company_name) { $this->company_name = $company_name; }
    public function setType($type) { $this->type = $type; }
    public function setStartDate($start_date) { $this->start_date = $start_date; }
    public function setEndDate($end_date) { $this->end_date = $end_date; }
    public function setTechStack($tech_stack) { $this->tech_stack = $tech_stack; }
}
?>
