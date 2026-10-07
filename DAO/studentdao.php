<?php
require_once __DIR__ . '/../model/student.php';
require_once __DIR__ . '/../config/Database.php';

class studentdao {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function getAllStudents() {
        $students = [];
        try {
            $stmt = $this->conn->query("SELECT * FROM students ORDER BY last_name ASC");
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
        try {
            $stmt = $this->conn->prepare("SELECT * FROM students WHERE id = ?");
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
        try {
            $stmt = $this->conn->prepare("INSERT INTO students (first_name, last_name, major, level, email) VALUES (?, ?, ?, ?, ?)");
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
        try {
            $stmt = $this->conn->prepare("UPDATE students SET first_name = ?, last_name = ?, major = ?, level = ?, email = ? WHERE id = ?");
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
        try {
            $stmt = $this->conn->prepare("DELETE FROM students WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Erreur deleteStudent: " . $e->getMessage());
            return false;
        }
    }

    public function countStudents() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM students");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
}

