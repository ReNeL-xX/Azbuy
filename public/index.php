<?php
session_start();

$action = $_GET['action'] ?? 'home';

// Define the base path - NOW POINTS TO APP FOLDER
define('BASE_PATH', dirname(__DIR__) . '/app');

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'login-process') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Simple demo login
        if ($username === 'demo' && $password === 'demo123') {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'demo';
            $_SESSION['success'] = 'Login successful! Welcome back!';
            header('Location: index.php?action=dashboard');
            exit;
        } else {
            $_SESSION['error'] = 'Invalid username or password. Try: demo / demo123';
            header('Location: index.php?action=login');
            exit;
        }
    }
    
    if ($action === 'register-process') {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (!empty($username) && !empty($email) && !empty($password)) {
            $_SESSION['success'] = 'Registration successful! Please login.';
            header('Location: index.php?action=login');
            exit;
        } else {
            $_SESSION['error'] = 'Please fill in all required fields.';
            header('Location: index.php?action=register');
            exit;
        }
    }
}

// Handle logout
if ($action === 'logout') {
    session_destroy();
    header('Location: index.php?action=home');
    exit;
}

// Map actions to view files - NOW USING APP FOLDER
$views = [
    'home' => BASE_PATH . '/views/pages/home.php',
    'about' => BASE_PATH . '/views/pages/about.php',
    'login' => BASE_PATH . '/views/auth/login.php',
    'register' => BASE_PATH . '/views/auth/register.php',
    'dashboard' => BASE_PATH . '/views/auction/dashboard.php',
    'create-auction' => BASE_PATH . '/views/auction/create_auction.php',
    'view-auction' => BASE_PATH . '/views/auction/view_auction.php',
    'my-auctions' => BASE_PATH . '/views/auction/my_auctions.php',
    'my-bids' => BASE_PATH . '/views/auction/my_bids.php',
    'settings' => BASE_PATH . '/views/user/settings.php',
];

if (isset($views[$action]) && file_exists($views[$action])) {
    require_once $views[$action];
} else {
    require_once BASE_PATH . '/views/pages/home.php';
}
?>