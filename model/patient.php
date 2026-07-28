<?php
class patient {
    private $id;
    private $user_id;
    private $full_name;
    private $birth_date;
    private $phone;
    private $email;
    private $created_at;

    public function __construct($id, $user_id, $full_name, $birth_date, $phone, $email, $created_at) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->full_name = $full_name;
        $this->birth_date = $birth_date;
        $this->phone = $phone;
        $this->email = $email;
        $this->created_at = $created_at;
    }

    public function getId() {
        return $this->id; 
    }
    public function getUserId() {
        return $this->user_id;
    }

    public function getName() {
        return $this->full_name;
    }

    public function getFullName() {
        return $this->full_name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getBirthDate() {
        return $this->birth_date;
    }


    public function getCreatedAt() {
        return $this->created_at;
    }
}