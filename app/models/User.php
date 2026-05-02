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
        $sql = "SELECT id, username, email, password, full_name, balance, profile_pic, is_active, is_admin 
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
        $sql = "SELECT id, username, email, full_name, phone, address, profile_pic, balance, created_at, is_admin 
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
    

    public function isAdmin($user_id): bool {
        $sql = "SELECT is_admin FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user && $user['is_admin'] == 1;
    }
    
  
    public function logAdminAction($admin_id, $action, $details) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $sql = "INSERT INTO admin_logs (admin_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isss", $admin_id, $action, $details, $ip);
        $stmt->execute();
        $stmt->close();
    }
    // Add these methods to the User class

/**
 * Enable 2FA for a user
 */
public function enableTwoFactor($userId, $secret): bool {
    $sql = "UPDATE users SET two_factor_secret = ?, two_factor_enabled = 1 WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("si", $secret, $userId);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Disable 2FA for a user
 */
public function disableTwoFactor($userId): bool {
    $sql = "UPDATE users SET two_factor_secret = NULL, two_factor_enabled = 0, two_factor_backup_codes = NULL WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Save backup codes for a user
 */
public function saveBackupCodes($userId, array $codes): bool {
    $codesJson = json_encode($codes);
    $sql = "UPDATE users SET two_factor_backup_codes = ? WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("si", $codesJson, $userId);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Get user's 2FA status
 */
public function getTwoFactorStatus($userId): array {
    $sql = "SELECT two_factor_secret, two_factor_enabled, two_factor_backup_codes FROM users WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    return [
        'enabled' => $user['two_factor_enabled'] == 1,
        'secret' => $user['two_factor_secret'],
        'backup_codes' => $user['two_factor_backup_codes'] ? json_decode($user['two_factor_backup_codes'], true) : []
    ];
}

/**
 * Verify 2FA code for login
 */
public function verifyTwoFactorCode($userId, $code): bool {
    $sql = "SELECT two_factor_secret, two_factor_enabled FROM users WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user || $user['two_factor_enabled'] != 1) {
        return false;
    }
    
    $twoFactorAuth = new TwoFactorAuth();
    return $twoFactorAuth->verifyCode($user['two_factor_secret'], $code);
}

}


?>