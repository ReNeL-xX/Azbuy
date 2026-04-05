<?php
class User {
    private mysqli $conn;
    
    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }
    
    public function register($username, $email, $password, $full_name = '', $phone = '') {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $email, $hashed_password, $full_name, $phone);
        return $stmt->execute();
    }
    
    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return false;
    }
    
    public function getUserById($id) {
        $sql = "SELECT id, username, email, full_name, phone, address, profile_pic, balance, created_at FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function updateProfile($id, $full_name, $phone, $address) {
        $sql = "UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $full_name, $phone, $address, $id);
        return $stmt->execute();
    }
    
    public function updateBalance($id, $amount) {
        $sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("di", $amount, $id);
        return $stmt->execute();
    }
}