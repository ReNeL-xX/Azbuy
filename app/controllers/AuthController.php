<?php
if (!class_exists('AuthController')) {

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private function connectDB(): mysqli {
        require_once __DIR__ . '/../../config/Database.php';
        $database = new Database();
        return $database->connect();
    }
    
    public function showLogin() {
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?action=dashboard');
            exit;
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    public function showRegister() {
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?action=dashboard');
            exit;
        }
        require_once __DIR__ . '/../views/auth/register.php';
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=login');
            exit;
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Please enter username/email and password';
            header('Location: index.php?action=login');
            exit;
        }
        
        $conn = $this->connectDB();
        $userModel = new User($conn);
        $result = $userModel->login($username, $password);
        $conn->close();
        
        if ($result['success']) {
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['username'] = $result['user']['username'];
            $_SESSION['user_email'] = $result['user']['email'];
            $_SESSION['user_balance'] = $result['user']['balance'];
            $_SESSION['success'] = $result['message'];
            header('Location: index.php?action=dashboard');
            exit;
        } else {
            $_SESSION['error'] = $result['message'];
            header('Location: index.php?action=login');
            exit;
        }
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=register');
            exit;
        }
        
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        // Validation
        $errors = [];
        
        if (empty($username)) {
            $errors[] = 'Username is required';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters';
        }
        
        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }
        
        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header('Location: index.php?action=register');
            exit;
        }
        
        $conn = $this->connectDB();
        $userModel = new User($conn);
        $result = $userModel->register($username, $email, $password, $full_name, $phone);
        $conn->close();
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'] . ' Please login.';
            header('Location: index.php?action=login');
            exit;
        } else {
            $_SESSION['error'] = $result['message'];
            header('Location: index.php?action=register');
            exit;
        }
    }
    
    public function logout() {
        session_destroy();
        $_SESSION = [];
        header('Location: index.php?action=home');
        exit;
    }
}

} // end if class_exists check