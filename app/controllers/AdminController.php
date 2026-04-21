<?php
if (!class_exists('AdminController')) {

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Auction.php';

class AdminController {
    private function connectDB(): mysqli {
        require_once __DIR__ . '/../../config/Database.php';
        $database = new Database();
        return $database->connect();
    }
    
    private function checkAdmin() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
            header('Location: index.php?action=login');
            exit;
        }
    }
    
    public function dashboard() {
        $this->checkAdmin();
        
        $conn = $this->connectDB();
        
        // Get statistics
        $stats = [];
        
        // Total users
        $result = $conn->query("SELECT COUNT(*) as count FROM users");
        $stats['total_users'] = $result->fetch_assoc()['count'];
        
        // Total auctions
        $result = $conn->query("SELECT COUNT(*) as count FROM auctions");
        $stats['total_auctions'] = $result->fetch_assoc()['count'];
        
        // Active auctions
        $result = $conn->query("SELECT COUNT(*) as count FROM auctions WHERE status = 'active'");
        $stats['active_auctions'] = $result->fetch_assoc()['count'];
        
        // Total bids
        $result = $conn->query("SELECT COUNT(*) as count FROM bids");
        $stats['total_bids'] = $result->fetch_assoc()['count'];
        
        // Total revenue
        $result = $conn->query("SELECT SUM(winning_bid) as total FROM auctions WHERE status = 'paid'");
        $stats['total_revenue'] = $result->fetch_assoc()['total'] ?? 0;
        
        // Recent auctions
        $recent_auctions = $conn->query("SELECT a.*, u.username as seller_name FROM auctions a JOIN users u ON a.seller_id = u.id ORDER BY a.created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
        
        // Recent users
        $recent_users = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
        
        $conn->close();
        
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }
    
    public function manageAuctions() {
        $this->checkAdmin();
        
        $conn = $this->connectDB();
        $auctions = $conn->query("SELECT a.*, u.username as seller_name FROM auctions a JOIN users u ON a.seller_id = u.id ORDER BY a.created_at DESC")->fetch_all(MYSQLI_ASSOC);
        
        require_once __DIR__ . '/../views/admin/auctions.php';
    }
    
    public function editAuction() {
        $this->checkAdmin();
        
        $auction_id = $_GET['id'] ?? 0;
        
        $conn = $this->connectDB();
        $auctionModel = new Auction($conn);
        $auction = $auctionModel->getAuctionById($auction_id);
        $conn->close();
        
        if (!$auction) {
            $_SESSION['error'] = 'Auction not found';
            header('Location: index.php?action=admin-auctions');
            exit;
        }
        
        require_once __DIR__ . '/../views/admin/edit_auction.php';
    }
    
    public function updateAuction() {
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin-auctions');
            exit;
        }
        
        $auction_id = $_POST['auction_id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = $_POST['category'] ?? '';
        $current_price = floatval($_POST['current_price'] ?? 0);
        
        $conn = $this->connectDB();
        
        // Log admin action
        $userModel = new User($conn);
        $userModel->logAdminAction($_SESSION['user_id'], 'UPDATE_AUCTION', "Updated auction ID: $auction_id");
        
        $sql = "UPDATE auctions SET title = ?, description = ?, category = ?, current_price = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdi", $title, $description, $category, $current_price, $auction_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Auction updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update auction';
        }
        
        $stmt->close();
        $conn->close();
        
        header('Location: index.php?action=admin-auctions');
        exit;
    }
    
    public function deleteAuction() {
        $this->checkAdmin();
        
        $auction_id = $_GET['id'] ?? 0;
        
        $conn = $this->connectDB();
        
        // Log admin action
        $userModel = new User($conn);
        $userModel->logAdminAction($_SESSION['user_id'], 'DELETE_AUCTION', "Deleted auction ID: $auction_id");
        
        $sql = "DELETE FROM auctions WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $auction_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Auction deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete auction';
        }
        
        $stmt->close();
        $conn->close();
        
        header('Location: index.php?action=admin-auctions');
        exit;
    }
    
    public function manageUsers() {
        $this->checkAdmin();
        
        $conn = $this->connectDB();
        $users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
        $conn->close();
        
        require_once __DIR__ . '/../views/admin/users.php';
    }
    
    public function editUser() {
        $this->checkAdmin();
        
        $user_id = $_GET['id'] ?? 0;
        
        $conn = $this->connectDB();
        $userModel = new User($conn);
        $user = $userModel->getUserById($user_id);
        $conn->close();
        
        if (!$user) {
            $_SESSION['error'] = 'User not found';
            header('Location: index.php?action=admin-users');
            exit;
        }
        
        require_once __DIR__ . '/../views/admin/edit_user.php';
    }
    
    public function updateUser() {
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin-users');
            exit;
        }
        
        $user_id = $_POST['user_id'] ?? 0;
        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $balance = floatval($_POST['balance'] ?? 0);
        $is_admin = isset($_POST['is_admin']) ? intval($_POST['is_admin']) : 0;
        
        // Prevent admin from removing their own admin status
        if ($user_id == $_SESSION['user_id'] && $is_admin == 0) {
            $_SESSION['error'] = 'You cannot remove your own admin privileges';
            header('Location: index.php?action=admin-users');
            exit;
        }
        
        $conn = $this->connectDB();
        
        // Log admin action
        $userModel = new User($conn);
        $userModel->logAdminAction($_SESSION['user_id'], 'UPDATE_USER', "Updated user ID: $user_id, Admin status: $is_admin");
        
        $sql = "UPDATE users SET full_name = ?, phone = ?, address = ?, balance = ?, is_admin = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdii", $full_name, $phone, $address, $balance, $is_admin, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'User updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update user: ' . $stmt->error;
        }
        
        $stmt->close();
        $conn->close();
        
        header('Location: index.php?action=admin-users');
        exit;
    }
    
    public function deleteUser() {
        $this->checkAdmin();
        
        $user_id = $_GET['id'] ?? 0;
        
        // Prevent admin from deleting themselves
        if ($user_id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'You cannot delete your own account';
            header('Location: index.php?action=admin-users');
            exit;
        }
        
        $conn = $this->connectDB();
        
        // Log admin action
        $userModel = new User($conn);
        $userModel->logAdminAction($_SESSION['user_id'], 'DELETE_USER', "Deleted user ID: $user_id");
        
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'User deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete user';
        }
        
        $stmt->close();
        $conn->close();
        
        header('Location: index.php?action=admin-users');
        exit;
    }
}

} // end if class_exists check
?>