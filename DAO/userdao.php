<?php
require_once __DIR__ . '/../model/user.php';
require_once __DIR__ . '/../config/Database.php';

class userdao {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function getAllUsers() {
        $users = [];
        $stmt = $this->conn->query("SELECT * FROM users ORDER BY id DESC");
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            $users[] = new user($row['id'], $row['name'], $row['email'], $row['password'], $row['role'], $row['created_at']);
        }

        return $users;
    }

    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new user($row['id'], $row['name'], $row['email'], $row['password'], $row['role'], $row['created_at']);
        } else {
            return null;
        }
    }

    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if ($row) {
            return new user($row['id'], $row['name'], $row['email'], $row['password'], $row['role'], $row['created_at']);
        }
        return null;
    }

    public function createUser($user) {
        if ($this->getUserByEmail($user->getEmail())) {
            throw new Exception("Un utilisateur avec cet e-mail existe déjà.");
        }
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $user->getName(),
            $user->getEmail(),
            password_hash($user->getPassword(), PASSWORD_DEFAULT),
            $user->getRole(),
            $user->getCreatedAt()
        ]);
    }
    public function user_login($user) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$user->getEmail()]);
        $result = $stmt->fetch();
        if ($result && password_verify($user->getPassword(), $result['password'])) {
            return new user($result['id'], $result['name'], $result['email'], $result['password'], $result['role'], $result['created_at']);
        } else {
            return null;
        }
    }
    public function updateUser($user) {
        $password = $user->getPassword();
        if (empty($password)) {
            $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user->getId()]);
            $existing = $stmt->fetch();
            $passwordHash = $existing ? $existing['password'] : null;
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        }

        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?");
        return $stmt->execute([
            $user->getName(),
            $user->getEmail(),
            $passwordHash,
            $user->getRole(),
            $user->getId()
        ]);
    }
    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}   

