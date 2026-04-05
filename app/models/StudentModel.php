<?php
class StudentModel {
    private mysqli $conn;
    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }
    public function getAllStudents(): array {
        $sql = "SELECT id, name, course, email FROM students ORDER BY id ASC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }
        if (!$stmt->execute()) {
            $stmt->close();
            die("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        $stmt->close();
        return $students;
    }
    public function saveStudent(string $name, string $course, string $email): bool {
        $sql = "INSERT INTO students (name, course, email) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }
        $stmt->bind_param("sss", $name, $course, $email);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getStudentById(int $id): ?array {
        $sql = "SELECT id, name, course, email FROM students WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            $stmt->close();
            die("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $stmt->close();
        return $student ?: null;
    }

    public function updateStudent(int $id, string $name, string $course, string $email): bool {
        $sql = "UPDATE students SET name = ?, course = ?, email = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }
        $stmt->bind_param("sssi", $name, $course, $email, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function deleteStudent(int $id): bool {
        $sql = "DELETE FROM students WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}