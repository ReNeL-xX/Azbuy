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
        $transactions = $userModel->getTransactions($_SESSION['user_id'], 10);
        $conn->close();
        
        require_once __DIR__ . '/../views/user/settings.php';
    }
    
    public function showWallet() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        require_once __DIR__ . '/../views/user/wallet.php';
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
        $bio = trim($_POST['bio'] ?? '');
        
        $conn = $this->connectDB();
        $userModel = new User($conn);
        
        if ($userModel->updateProfile($_SESSION['user_id'], $full_name, $phone, $address, $bio)) {
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
    
    public function addFunds() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=wallet');
            exit;
        }
        
        $amount = floatval($_POST['amount'] ?? 0);
        
        if ($amount <= 0) {
            $_SESSION['error'] = 'Please enter a valid amount';
            header('Location: index.php?action=wallet');
            exit;
        }
        
        $conn = $this->connectDB();
        $userModel = new User($conn);
        
        // In a real app, you would process payment here (credit card, GCash, etc.)
        // For demo purposes, we'll just add the funds directly
        
        $description = "Added funds to wallet";
        if ($userModel->addBalance($_SESSION['user_id'], $amount, $description, null, 'deposit')) {
            $_SESSION['success'] = '₱' . number_format($amount, 2) . ' has been added to your wallet!';
            // Update session balance
            $_SESSION['user_balance'] = $userModel->getBalance($_SESSION['user_id']);
        } else {
            $_SESSION['error'] = 'Failed to add funds. Please try again.';
        }
        
        $conn->close();
        header('Location: index.php?action=wallet');
        exit;
    }


   public function uploadProfilePicture() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?action=login');
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?action=settings');
        exit;
    }
    
    if (!isset($_FILES['profile_pic']) || $_FILES['profile_pic']['error'] !== 0) {
        $_SESSION['error'] = 'Please select an image to upload';
        header('Location: index.php?action=settings');
        exit;
    }
    
    $file = $_FILES['profile_pic'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        $_SESSION['error'] = 'Only JPG, PNG, GIF, and WEBP images are allowed';
        header('Location: index.php?action=settings');
        exit;
    }
    
    if ($file['size'] > 2 * 1024 * 1024) {
        $_SESSION['error'] = 'Image size must be less than 2MB';
        header('Location: index.php?action=settings');
        exit;
    }
    
   // CORRECT PATH FOR HOSTINGER - NO AzBuy folder
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/profiles/';
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    $target_file = $upload_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        $conn = $this->connectDB();
        $userModel = new User($conn);
        // Store correct relative path
        $image_path = 'assets/uploads/profiles/' . $new_filename;
        
        if ($userModel->updateProfilePicture($_SESSION['user_id'], $image_path)) {
            $_SESSION['success'] = 'Profile picture updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update profile picture';
        }
        $conn->close();
    } else {
        $_SESSION['error'] = 'Failed to upload image';
    }
    
    header('Location: index.php?action=settings');
    exit;
}
}

} // end if class_exists check