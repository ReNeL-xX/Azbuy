<?php
if (!class_exists('TwoFactorController')) {

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/TwoFactorAuth.php';

class TwoFactorController {
    private function connectDB(): mysqli {
        require_once __DIR__ . '/../../config/Database.php';
        $database = new Database();
        return $database->connect();
    }
    
    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }
    
    /**
     * Show 2FA setup page
     */
    public function setup() {
        $this->checkAuth();
        
        $conn = $this->connectDB();
        $userModel = new User($conn);
        $twoFactorStatus = $userModel->getTwoFactorStatus($_SESSION['user_id']);
        $conn->close();
        
        $twoFactorAuth = new TwoFactorAuth();
        
        // Generate new secret if not already enabled
        if (!$twoFactorStatus['enabled']) {
            $secret = $twoFactorAuth->generateSecret();
            $qrCodeBase64 = $twoFactorAuth->generateQRCodeBase64($_SESSION['username'], $secret);
            $backupCodes = $twoFactorAuth->generateBackupCodes(10);
            
            // Store temporary secret in session for verification
            $_SESSION['2fa_temp_secret'] = $secret;
            $_SESSION['2fa_temp_backup_codes'] = $backupCodes;
        } else {
            $secret = $twoFactorStatus['secret'];
            $qrCodeBase64 = $twoFactorAuth->generateQRCodeBase64($_SESSION['username'], $secret);
            $backupCodes = $twoFactorStatus['backup_codes'];
        }
        
        require_once __DIR__ . '/../views/user/2fa_setup.php';
    }
    
    /**
     * Enable 2FA after verification
     */
    public function enable() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=2fa-setup');
            exit;
        }
        
        $code = trim($_POST['code'] ?? '');
        $tempSecret = $_SESSION['2fa_temp_secret'] ?? null;
        
        if (!$tempSecret) {
            $_SESSION['error'] = 'Invalid 2FA setup. Please try again.';
            header('Location: index.php?action=2fa-setup');
            exit;
        }
        
        $twoFactorAuth = new TwoFactorAuth();
        
        if ($twoFactorAuth->verifyCode($tempSecret, $code)) {
            $conn = $this->connectDB();
            $userModel = new User($conn);
            
            // Enable 2FA
            $userModel->enableTwoFactor($_SESSION['user_id'], $tempSecret);
            
            // Save backup codes
            $backupCodes = $_SESSION['2fa_temp_backup_codes'] ?? [];
            $userModel->saveBackupCodes($_SESSION['user_id'], $backupCodes);
            
            $conn->close();
            
            // Clear temporary session data
            unset($_SESSION['2fa_temp_secret']);
            unset($_SESSION['2fa_temp_backup_codes']);
            
            $_SESSION['success'] = 'Two-Factor Authentication has been enabled! Please save your backup codes.';
            header('Location: index.php?action=2fa-setup');
            exit;
        } else {
            $_SESSION['error'] = 'Invalid verification code. Please try again.';
            header('Location: index.php?action=2fa-setup');
            exit;
        }
    }
    
    /**
     * Disable 2FA
     */
    public function disable() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=2fa-setup');
            exit;
        }
        
        $code = trim($_POST['code'] ?? '');
        
        $conn = $this->connectDB();
        $userModel = new User($conn);
        
        // Verify the code before disabling
        if ($userModel->verifyTwoFactorCode($_SESSION['user_id'], $code)) {
            $userModel->disableTwoFactor($_SESSION['user_id']);
            $conn->close();
            
            $_SESSION['success'] = 'Two-Factor Authentication has been disabled.';
            header('Location: index.php?action=2fa-setup');
            exit;
        } else {
            $conn->close();
            $_SESSION['error'] = 'Invalid verification code. Cannot disable 2FA.';
            header('Location: index.php?action=2fa-setup');
            exit;
        }
    }
    
    /**
     * Show 2FA verification page during login
     */
    public function verify() {
        if (!isset($_SESSION['2fa_pending_user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        require_once __DIR__ . '/../views/auth/2fa_verify.php';
    }
    
    /**
     Process 2FA verification during login
     */
    public function processVerification() {
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
        
        // Check if it's a backup code
        $twoFactorStatus = $userModel->getTwoFactorStatus($userId);
        $twoFactorAuth = new TwoFactorAuth();
        
        $isValid = false;
        
        // Try regular 2FA code
        if ($twoFactorAuth->verifyCode($twoFactorStatus['secret'], $code)) {
            $isValid = true;
        }
        // Try backup code
        else if ($twoFactorAuth->verifyBackupCode($code, $twoFactorStatus['backup_codes'])) {
            $isValid = true;
            // Remove used backup code
            $newBackupCodes = array_values(array_diff($twoFactorStatus['backup_codes'], [$code]));
            $userModel->saveBackupCodes($userId, $newBackupCodes);
        }
        
        $conn->close();
        
        if ($isValid) {
            // Complete login
            $userData = $_SESSION['2fa_pending_user_data'];
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['user_email'] = $userData['email'];
            $_SESSION['user_balance'] = $userData['balance'];
            
            if ($userData['is_admin']) {
                $_SESSION['is_admin'] = true;
            }
            
            unset($_SESSION['2fa_pending_user_id']);
            unset($_SESSION['2fa_pending_user_data']);
            
            // Check if admin
            if ($userData['is_admin']) {
                header('Location: index.php?action=admin-dashboard');
            } else {
                header('Location: index.php?action=dashboard');
            }
            exit;
        } else {
            $_SESSION['error'] = 'Invalid verification code. Please try again.';
            header('Location: index.php?action=2fa-verify');
            exit;
        }
    }
}

} // end class_exists check