<?php
class student {
    private $id;
    private $first_name;
    private $last_name;
    private $major;
    private $level;
    private $email;
    private $created_at;

    public function __construct($id = null, $first_name = "", $last_name = "", $major = "", $level = "", $email = "", $created_at = null) {
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->major = $major;
        $this->level = $level;
        $this->email = $email;
        $this->created_at = $created_at;
    }

    public function getId() { return $this->id; }
    public function getFirstName() { return $this->first_name; }
    public function getLastName() { return $this->last_name; }
    public function getMajor() { return $this->major; }
    public function getLevel() { return $this->level; }
    public function getEmail() { return $this->email; }
    public function getCreatedAt() { return $this->created_at; }

    public function setId($id) { $this->id = $id; }
    public function setFirstName($first_name) { $this->first_name = $first_name; }
    public function setLastName($last_name) { $this->last_name = $last_name; }
    public function setMajor($major) { $this->major = $major; }
    public function setLevel($level) { $this->level = $level; }
    public function setEmail($email) { $this->email = $email; }
}
?>
