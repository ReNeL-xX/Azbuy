<?php
if (!class_exists('AuctionController')) {

require_once __DIR__ . '/../models/Auction.php';
require_once __DIR__ . '/../models/User.php';

class AuctionController {
    private function connectDB(): mysqli {
        require_once __DIR__ . '/../../config/Database.php';
        $database = new Database();
        return $database->connect();
    }
    
    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $conn = $this->connectDB();
        $auctionModel = new Auction($conn);
        
        // Check for ended auctions
        $auctionModel->checkEndedAuctions();
        
        // Get active auctions
        $active_auctions = $auctionModel->getAllActive(12, 0);
        $conn->close();
        
        require_once __DIR__ . '/../views/auction/dashboard.php';
    }
    
    public function showCreateAuction() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        require_once __DIR__ . '/../views/auction/create_auction.php';
    }
    
    public function createAuction() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=create-auction');
            exit;
        }
        
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = $_POST['category'] ?? '';
        $starting_price = floatval($_POST['starting_price'] ?? 0);
        $duration_hours = intval($_POST['duration_hours'] ?? 24);
        $bid_increment = floatval($_POST['bid_increment'] ?? 1.00);
        
        // Use server time for consistency
        $now = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $end_time = $now->modify("+{$duration_hours} hours")->format('Y-m-d H:i:s');
        
        // Validation
        $errors = [];
        if (empty($title)) {
            $errors[] = 'Title is required';
        }
        if (empty($description)) {
            $errors[] = 'Description is required';
        }
        if (empty($category)) {
            $errors[] = 'Category is required';
        }
        if ($starting_price < 1) {
$errors[] = 'Starting price must be at least ₱1.00';
        }
        if ($bid_increment < 0.01) {
$errors[] = 'Bid increment must be at least ₱0.01';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header('Location: index.php?action=create-auction');
            exit;
        }
        
        // Handle image upload
        $image_url = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed)) {
                $_SESSION['error'] = 'Only JPG, PNG, GIF, and WEBP images are allowed';
                header('Location: index.php?action=create-auction');
                exit;
            }
            
            $upload_dir = __DIR__ . '/../../public/assets/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
            $target_file = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = 'assets/uploads/' . $new_filename;
            } else {
                $_SESSION['error'] = 'Failed to upload image';
                header('Location: index.php?action=create-auction');
                exit;
            }
        }
        
        $conn = $this->connectDB();
        $auctionModel = new Auction($conn);
        $result = $auctionModel->create($_SESSION['user_id'], $title, $description, $category, $starting_price, $end_time, $image_url, $bid_increment);
        $conn->close();
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
            header('Location: index.php?action=dashboard');
            exit;
        } else {
            $_SESSION['error'] = $result['message'];
            header('Location: index.php?action=create-auction');
            exit;
        }
    }
    
    public function viewAuction() {
        $id = $_GET['id'] ?? 0;
        
        $conn = $this->connectDB();
        $auctionModel = new Auction($conn);
        $auction = $auctionModel->getAuctionById($id);
        
        // Get bid history
        $bid_sql = "SELECT b.*, u.username FROM bids b JOIN users u ON b.bidder_id = u.id WHERE b.auction_id = ? ORDER BY b.amount DESC LIMIT 20";
        $stmt = $conn->prepare($bid_sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $bid_history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        $conn->close();
        
        if (!$auction) {
            $_SESSION['error'] = "Auction not found";
            header('Location: index.php?action=dashboard');
            exit;
        }
        
        require_once __DIR__ . '/../views/auction/view_auction.php';
    }
    
    public function placeBid() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please login to bid']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $auction_id = intval($_POST['auction_id'] ?? 0);
        $amount = floatval($_POST['amount'] ?? 0);
        
        if ($auction_id <= 0 || $amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid bid amount']);
            exit;
        }
        
        $conn = $this->connectDB();
        $auctionModel = new Auction($conn);
        $result = $auctionModel->placeBid($auction_id, $_SESSION['user_id'], $amount);
        $conn->close();
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
    
    public function myAuctions() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $conn = $this->connectDB();
        $auctionModel = new Auction($conn);
        $my_auctions = $auctionModel->getUserAuctions($_SESSION['user_id']);
        $conn->close();
        
        require_once __DIR__ . '/../views/auction/my_auctions.php';
    }
    
    public function deleteAuction() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $id = $_GET['id'] ?? 0;
        
        $conn = $this->connectDB();
        $auctionModel = new Auction($conn);
        $result = $auctionModel->deleteAuction($id, $_SESSION['user_id']);
        $conn->close();
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        header('Location: index.php?action=my-auctions');
        exit;
    }
    
    public function myBids() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $conn = $this->connectDB();
        $auctionModel = new Auction($conn);
        $my_bids = $auctionModel->getUserBids($_SESSION['user_id']);
        $my_auctions = $auctionModel->getUserAuctions($_SESSION['user_id']);
        $conn->close();
        
        require_once __DIR__ . '/../views/auction/my_bids.php';
    }
    
   public function payForAuction() {
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login to pay']);
        exit;
    }
    
    $auction_id = $_GET['id'] ?? 0;
    
    if ($auction_id <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid auction ID']);
        exit;
    }
    
    $conn = $this->connectDB();
    $auctionModel = new Auction($conn);
    $result = $auctionModel->processPayment($auction_id, $_SESSION['user_id']);
    $conn->close();
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
    
    public function cancelBid() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $bid_id = $_GET['id'] ?? 0;
        
        if ($bid_id <= 0) {
            $_SESSION['error'] = 'Invalid bid ID';
            header('Location: index.php?action=my-bids');
            exit;
        }
        
        $conn = $this->connectDB();
        $auctionModel = new Auction($conn);
        
        $bid = $auctionModel->getBidById($bid_id, $_SESSION['user_id']);
        
        if (!$bid) {
            $conn->close();
            $_SESSION['error'] = 'Bid not found or you do not have permission';
            header('Location: index.php?action=my-bids');
            exit;
        }
        
        if ($bid['status'] != 'active') {
            $conn->close();
            $_SESSION['error'] = 'Cannot cancel bid on ended auction';
            header('Location: index.php?action=my-bids');
            exit;
        }
        
        $result = $auctionModel->deleteBid($bid_id, $_SESSION['user_id']);
        $conn->close();
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        header('Location: index.php?action=my-bids');
        exit;
    }
    
    public function showEditAuction() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $auction_id = $_GET['id'] ?? 0;
        
        if ($auction_id <= 0) {
            $_SESSION['error'] = 'Invalid auction ID';
            header('Location: index.php?action=my-auctions');
            exit;
        }
        
        $conn = $this->connectDB();
        $auctionModel = new Auction($conn);
        $auction = $auctionModel->getAuctionForEdit($auction_id, $_SESSION['user_id']);
        $conn->close();
        
        if (!$auction) {
            $_SESSION['error'] = 'Auction not found or you do not have permission to edit it';
            header('Location: index.php?action=my-auctions');
            exit;
        }
        
        require_once __DIR__ . '/../views/auction/edit_auction.php';
    }
    
    public function updateAuction() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=my-auctions');
            exit;
        }
        
        $auction_id = $_POST['auction_id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = $_POST['category'] ?? '';
        $duration_hours = intval($_POST['duration_hours'] ?? 24);
        
        // Calculate new end time
        $now = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $end_time = $now->modify("+{$duration_hours} hours")->format('Y-m-d H:i:s');
        
        // Validation
        $errors = [];
        if (empty($title)) {
            $errors[] = 'Title is required';
        }
        if (empty($description)) {
            $errors[] = 'Description is required';
        }
        if (empty($category)) {
            $errors[] = 'Category is required';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header('Location: index.php?action=edit-auction&id=' . $auction_id);
            exit;
        }
        
        // Handle image upload
        $image_url = null;
        $keep_image = isset($_POST['keep_image']) ? true : false;
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $upload_dir = __DIR__ . '/../../public/assets/uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_filename)) {
                    $image_url = 'assets/uploads/' . $new_filename;
                }
            }
        }
        
        $conn = $this->connectDB();
        $auctionModel = new Auction($conn);
        $result = $auctionModel->updateAuction($auction_id, $_SESSION['user_id'], $title, $description, $category, $end_time, $image_url);
        $conn->close();
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
            header('Location: index.php?action=my-auctions');
            exit;
        } else {
            $_SESSION['error'] = $result['message'];
            header('Location: index.php?action=edit-auction&id=' . $auction_id);
            exit;
        }
    }

    public function getNotifications() {
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login']);
        exit;
    }
    
    $conn = $this->connectDB();
    $auctionModel = new Auction($conn);
    $notifications = $auctionModel->getNotifications($_SESSION['user_id']);
    $unread_count = $auctionModel->getUnreadNotificationCount($_SESSION['user_id']);
    $conn->close();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unread_count
    ]);
    exit;
}

public function markNotificationRead() {
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login']);
        exit;
    }
    
    $notification_id = $_POST['notification_id'] ?? 0;
    
    $conn = $this->connectDB();
    $auctionModel = new Auction($conn);
    $result = $auctionModel->markNotificationRead($notification_id, $_SESSION['user_id']);
    $conn->close();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
    exit;
}

public function deleteNotification() {
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login']);
        exit;
    }
    
    $notification_id = $_POST['notification_id'] ?? 0;
    
    if ($notification_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
        exit;
    }
    
    $conn = $this->connectDB();
    $auctionModel = new Auction($conn);
    $result = $auctionModel->deleteNotification($notification_id, $_SESSION['user_id']);
    $conn->close();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
    exit;
}

public function deleteAllNotifications() {
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login']);
        exit;
    }
    
    $conn = $this->connectDB();
    $auctionModel = new Auction($conn);
    $result = $auctionModel->deleteAllNotifications($_SESSION['user_id']);
    $conn->close();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
    exit;
}

}

} // end if class_exists check
?>