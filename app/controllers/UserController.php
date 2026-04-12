<?php
if (!class_exists('UserController')) {

require_once __DIR__ . '/../models/User.php';

class UserController {
    private function connectDB(): mysqli {
        require_once __DIR__ . '/../../config/Database.php';
        $database = new Database();
        return $database->connect();
    }
    
    public function showSettings() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $conn = $this->connectDB();
        $userModel = new User($conn);
        $user = $userModel->getUserById($_SESSION['user_id']);
        $conn->close();
        
        require_once __DIR__ . '/../views/user/settings.php';
    }
    
    public function updateProfile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=settings');
            exit;
        }
        
        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        
        $conn = $this->connectDB();
        $userModel = new User($conn);
        
        if ($userModel->updateProfile($_SESSION['user_id'], $full_name, $phone, $address)) {
            $_SESSION['success'] = 'Profile updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update profile';
        }
        
        $conn->close();
        header('Location: index.php?action=settings');
        exit;
    }
    
    public function updatePassword() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=settings');
            exit;
        }
        
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = 'New passwords do not match';
            header('Location: index.php?action=settings');
            exit;
        }
        
        if (strlen($new_password) < 6) {
            $_SESSION['error'] = 'New password must be at least 6 characters';
            header('Location: index.php?action=settings');
            exit;
        }
        
        $conn = $this->connectDB();
        $userModel = new User($conn);
        $result = $userModel->updatePassword($_SESSION['user_id'], $current_password, $new_password);
        $conn->close();
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        header('Location: index.php?action=settings');
        exit;
    }
}

} // end if class_exists check