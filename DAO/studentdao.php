<?php
require_once __DIR__ . '/../model/student.php';

class studentdao {
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

    public function getAllStudents() {
        $conn = $this->connect();
        if (!$conn) {
            return [];
        }

        $students = [];
        try {
            $stmt = $conn->query("SELECT * FROM students ORDER BY last_name ASC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $students[] = new student(
                    $row['id'],
                    $row['first_name'],
                    $row['last_name'],
                    $row['major'],
                    $row['level'],
                    $row['email'],
                    $row['created_at']
                );
            }
        } catch (PDOException $e) {
            error_log("Erreur getAllStudents: " . $e->getMessage());
        }
        return $students;
    }

    public function getStudentById($id) {
        $conn = $this->connect();
        if (!$conn) {
            return null;
        }

        try {
            $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
            $stmt->execute([$id]);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new Student(
                    $row['id'],
                    $row['first_name'],
                    $row['last_name'],
                    $row['major'],
                    $row['level'],
                    $row['email'],
                    $row['created_at']
                );
            }
        } catch (PDOException $e) {
            error_log("Erreur getStudentById: " . $e->getMessage());
        }
        return null;
    }

    public function createStudent(Student $student) {
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("INSERT INTO students (first_name, last_name, major, level, email) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([
                $student->getFirstName(),
                $student->getLastName(),
                $student->getMajor(),
                $student->getLevel(),
                $student->getEmail()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur createStudent: " . $e->getMessage());
            return false;
        }
    }

    public function updateStudent(Student $student) {
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("UPDATE students SET first_name = ?, last_name = ?, major = ?, level = ?, email = ? WHERE id = ?");
            return $stmt->execute([
                $student->getFirstName(),
                $student->getLastName(),
                $student->getMajor(),
                $student->getLevel(),
                $student->getEmail(),
                $student->getId()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur updateStudent: " . $e->getMessage());
            return false;
        }
    }

    public function deleteStudent($id) {
        $conn = $this->connect();
        if (!$conn) {
            return false;
        }

        try {
            $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Erreur deleteStudent: " . $e->getMessage());
            return false;
        }
    }

    public function countStudents() {
        $conn = $this->connect();
        if (!$conn) {
            return 0;
        }

        try {
            $stmt = $conn->query("SELECT COUNT(*) FROM students");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
}
