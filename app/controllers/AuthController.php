<?php
if (!class_exists('AuthController')) {

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/TwoFactorAuth.php';

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
        
        if ($result['success']) {
            $userId = $result['user']['id'];
            $twoFactorStatus = $userModel->getTwoFactorStatus($userId);
            
            $twoFactorAuth = new TwoFactorAuth();
            
            if ($twoFactorStatus['enabled']) {
                // 2FA already set up - verify code
                $conn->close();
                
                // Use generateQRCodeHtml method
                $qrCodeHtml = $twoFactorAuth->generateQRCodeHtml(
                    $result['user']['email'],
                    $twoFactorStatus['secret']
                );
                
                $_SESSION['2fa_pending_user_id'] = $userId;
                $_SESSION['2fa_pending_user_data'] = $result['user'];
                $_SESSION['2fa_qr_code'] = $qrCodeHtml;
                $_SESSION['2fa_mode'] = 'verify';
                
                require_once __DIR__ . '/../views/auth/2fa_verify.php';
                exit;
            } else {
                // First time login - need to setup 2FA
                $conn->close();
                
                $secret = $twoFactorAuth->generateSecret();
                // Use generateQRCodeHtml method
                $qrCodeHtml = $twoFactorAuth->generateQRCodeHtml($result['user']['email'], $secret);
                
                $_SESSION['2fa_pending_user_id'] = $userId;
                $_SESSION['2fa_pending_user_data'] = $result['user'];
                $_SESSION['2fa_temp_secret'] = $secret;
                $_SESSION['2fa_qr_code'] = $qrCodeHtml;
                $_SESSION['2fa_mode'] = 'setup';
                
                require_once __DIR__ . '/../views/auth/2fa_setup_required.php';
                exit;
            }
        } else {
            $conn->close();
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
            $_SESSION['success'] = $result['message'] . ' Please login to complete 2FA setup.';
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
    
   public function process2FASetup() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?action=login');
        exit;
    }
    
    $code = trim($_POST['code'] ?? '');
    $userId = $_SESSION['2fa_pending_user_id'] ?? null;
    $tempSecret = $_SESSION['2fa_temp_secret'] ?? null;
    
    if (!$userId || !$tempSecret) {
        $_SESSION['error'] = 'Invalid 2FA setup. Please login again.';
        header('Location: index.php?action=login');
        exit;
    }
    
    $twoFactorAuth = new TwoFactorAuth();
    
    if ($twoFactorAuth->verifyCode($tempSecret, $code)) {
        $conn = $this->connectDB();
        $userModel = new User($conn);
        
        // Enable 2FA for the user
        $userModel->enableTwoFactor($userId, $tempSecret);
        $conn->close();
        
        // Complete login
        $userData = $_SESSION['2fa_pending_user_data'];
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['username'] = $userData['username'];
        $_SESSION['user_email'] = $userData['email'];
        $_SESSION['user_balance'] = $userData['balance'];
        
        if ($userData['is_admin']) {
            $_SESSION['is_admin'] = true;
        }
        
        // Clear 2FA session data
        unset($_SESSION['2fa_pending_user_id']);
        unset($_SESSION['2fa_pending_user_data']);
        unset($_SESSION['2fa_temp_secret']);
        unset($_SESSION['2fa_qr_code']);
        unset($_SESSION['2fa_mode']);
        
        $_SESSION['success'] = 'Welcome to AzBuy, ' . $userData['username'] . '! Your account is now secured with 2FA.';
        header('Location: index.php?action=dashboard');
        exit;
    } else {
        $_SESSION['error'] = 'Invalid verification code. Please try again.';
        header('Location: index.php?action=login');
        exit;
    }
}
    
    public function process2FAVerify() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=login');
            exit;
        }
        
        $code = trim($_POST['code'] ?? '');
        $userId = $_SESSION['2fa_pending_user_id'] ?? null;
        
        if (!$userId) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $conn = $this->connectDB();
        $userModel = new User($conn);
        $twoFactorStatus = $userModel->getTwoFactorStatus($userId);
        
        $twoFactorAuth = new TwoFactorAuth();
        
        $isValid = false;
        
        // Try regular 2FA code
        if ($twoFactorAuth->verifyCode($twoFactorStatus['secret'], $code)) {
            $isValid = true;
        }
        
        $conn->close();
        
        if ($isValid) {
            $userData = $_SESSION['2fa_pending_user_data'];
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['user_email'] = $userData['email'];
            $_SESSION['user_balance'] = $userData['balance'];
            
            if ($userData['is_admin']) {
                $_SESSION['is_admin'] = true;
            }
            
            // Clear 2FA session data
            unset($_SESSION['2fa_pending_user_id']);
            unset($_SESSION['2fa_pending_user_data']);
            unset($_SESSION['2fa_qr_code']);
            unset($_SESSION['2fa_mode']);
            
            // Redirect directly to home page
            $_SESSION['success'] = 'Welcome back, ' . $userData['username'] . '!';
            header('Location: index.php?action=dashboard');
            exit;
        } else {
            $_SESSION['error'] = 'Invalid verification code. Please try again.';
            header('Location: index.php?action=login');
            exit;
        }
    }
}

} // end if class_exists check