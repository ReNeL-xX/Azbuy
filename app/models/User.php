<?php
class User {
    private mysqli $conn;
    
    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }
    
    // Register new user
    public function register($username, $email, $password, $full_name = '', $phone = ''): array {
        // Check if username exists
        $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $check_stmt = $this->conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }
        $check_stmt->close();
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $sql = "INSERT INTO users (username, email, password, full_name, phone, balance) 
                VALUES (?, ?, ?, ?, ?, 100.00)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $email, $hashed_password, $full_name, $phone);
        
        if ($stmt->execute()) {
            $user_id = $this->conn->insert_id;
            $stmt->close();
            return ['success' => true, 'message' => 'Registration successful!', 'user_id' => $user_id];
        }
        
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Registration failed: ' . $error];
    }
    
    // Login user
    public function login($username, $password): array {
        $sql = "SELECT id, username, email, password, full_name, balance, profile_pic, is_active 
                FROM users WHERE username = ? OR email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_active'] == 0) {
                return ['success' => false, 'message' => 'Account is deactivated'];
            }
            
            unset($user['password']);
            return ['success' => true, 'message' => 'Login successful!', 'user' => $user];
        }
        
        return ['success' => false, 'message' => 'Invalid username/email or password'];
    }
    
    // Get user by ID
    public function getUserById($id): ?array {
        $sql = "SELECT id, username, email, full_name, phone, address, profile_pic, balance, created_at 
                FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }
    
    // Update user profile
    public function updateProfile($id, $full_name, $phone, $address): bool {
        $sql = "UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $full_name, $phone, $address, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    
    // Update password
    public function updatePassword($id, $current_password, $new_password): array {
        // Get current password hash
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if (!$user || !password_verify($current_password, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET password = ? WHERE id = ?";
        $update_stmt = $this->conn->prepare($update_sql);
        $update_stmt->bind_param("si", $hashed_password, $id);
        
        if ($update_stmt->execute()) {
            $update_stmt->close();
            return ['success' => true, 'message' => 'Password updated successfully'];
        }
        
        $error = $update_stmt->error;
        $update_stmt->close();
        return ['success' => false, 'message' => 'Failed to update password: ' . $error];
    }
    
    // Update balance
    public function updateBalance($id, $amount): bool {
        $sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("di", $amount, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>