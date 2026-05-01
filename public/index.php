<?php
session_start();

require_once __DIR__ . '/../config/Database.php';

$action = $_GET['action'] ?? 'home';

// Define the base path
define('BASE_PATH', dirname(__DIR__) . '/app');

// Include Controllers (with path verification)
$controllers_path = BASE_PATH . '/controllers/';

$auth_file = $controllers_path . 'AuthController.php';
$user_file = $controllers_path . 'UserController.php';
$auction_file = $controllers_path . 'AuctionController.php';
$admin_file = $controllers_path . 'AdminController.php';

// Check if files exist before including
if (file_exists($auth_file)) {
    require_once $auth_file;
} else {
    die('AuthController.php not found at: ' . $auth_file);
}

if (file_exists($user_file)) {
    require_once $user_file;
} else {
    die('UserController.php not found at: ' . $user_file);
}

if (file_exists($auction_file)) {
    require_once $auction_file;
} else {
    die('AuctionController.php not found at: ' . $auction_file);
}

if (file_exists($admin_file)) {
    require_once $admin_file;
} else {
    die('AdminController.php not found at: ' . $admin_file);
}

// Create controller instances
$authController = new AuthController();
$userController = new UserController();
$auctionController = new AuctionController();
$adminController = new AdminController();

switch ($action) {
    // Public routes
    case 'home':
        require_once BASE_PATH . '/views/pages/home.php';
        break;
    
    case 'about':
        require_once BASE_PATH . '/views/pages/about.php';
        break;
    
    case 'login':
        $authController->showLogin();
        break;
    
    case 'login-process':
        $authController->login();
        break;
    
    case 'register':
        $authController->showRegister();
        break;
    
    case 'register-process':
        $authController->register();
        break;
    
    case 'logout':
        $authController->logout();
        break;
    
    // Auction routes
    case 'dashboard':
        $auctionController->dashboard();
        break;
    
    case 'create-auction':
        $auctionController->showCreateAuction();
        break;
    
    case 'create-auction-process':
        $auctionController->createAuction();
        break;
    
    case 'view-auction':
        $auctionController->viewAuction();
        break;
    
    case 'place-bid':
        $auctionController->placeBid();
        break;
    
    case 'my-auctions':
        $auctionController->myAuctions();
        break;
    
    case 'delete-auction':
        $auctionController->deleteAuction();
        break;
    
    case 'my-bids':
        $auctionController->myBids();
        break;
    
    case 'pay-auction':
        $auctionController->payForAuction();
        break;
    
    case 'cancel-bid':
        $auctionController->cancelBid();
        break;
    
    case 'edit-auction':
        $auctionController->showEditAuction();
        break;
    
    case 'update-auction':
        $auctionController->updateAuction();
        break;
    
    // Admin routes
    case 'admin-dashboard':
        $adminController->dashboard();
        break;
    
    case 'admin-auctions':
        $adminController->manageAuctions();
        break;
    
    case 'admin-edit-auction':
        $adminController->editAuction();
        break;
    
    case 'admin-update-auction':
        $adminController->updateAuction();
        break;
    
    case 'admin-delete-auction':
        $adminController->deleteAuction();
        break;
    
    case 'admin-users':
        $adminController->manageUsers();
        break;
    
    case 'admin-edit-user':
        $adminController->editUser();
        break;
    
    case 'admin-update-user':
        $adminController->updateUser();
        break;
    
    case 'admin-delete-user':
        $adminController->deleteUser();
        break;
    
    // User routes
    case 'settings':
        $userController->showSettings();
        break;
    
    case 'update-profile':
        $userController->updateProfile();
        break;
    
    case 'update-password':
        $userController->updatePassword();
        break;

     case 'get-notifications':
        $auctionController->getNotifications();
        break;

    case 'mark-notification-read':
        $auctionController->markNotificationRead();
        break; 
    
    case 'delete-notification':
    $auctionController->deleteNotification();
    break;

    case 'delete-all-notifications':
        $auctionController->deleteAllNotifications();
        break;    
    default:
        require_once BASE_PATH . '/views/pages/home.php';
        break;
}
?>