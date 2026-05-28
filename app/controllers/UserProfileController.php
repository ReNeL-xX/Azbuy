<?php
if (!class_exists('UserProfileController')) {

require_once __DIR__ . '/../models/User.php';

class UserProfileController {
    private function connectDB(): mysqli {
        require_once __DIR__ . '/../../config/Database.php';
        $database = new Database();
        return $database->connect();
    }
    
    public function showProfile() {
        $user_id = $_GET['id'] ?? 0;
        
        if ($user_id <= 0) {
            $_SESSION['error'] = 'User not found';
            header('Location: index.php?action=home');
            exit;
        }
        
        $conn = $this->connectDB();
        $userModel = new User($conn);
        $profile = $userModel->getPublicProfile($user_id);
        $conn->close();
        
        if (!$profile) {
            $_SESSION['error'] = 'User not found';
            header('Location: index.php?action=home');
            exit;
        }
        
        require_once __DIR__ . '/../views/user/profile.php';
    }
}

} // end class_exists check