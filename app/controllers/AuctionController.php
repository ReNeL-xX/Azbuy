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
        $end_time = date('Y-m-d H:i:s', strtotime("+{$duration_hours} hours"));
        
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
            $errors[] = 'Starting price must be at least $1.00';
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
        $result = $auctionModel->create($_SESSION['user_id'], $title, $description, $category, $starting_price, $end_time, $image_url);
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
            header('Location: index.php?action=login');
            exit;
        }
        
        $auction_id = $_GET['id'] ?? 0;
        
        $conn = $this->connectDB();
        $auctionModel = new Auction($conn);
        $result = $auctionModel->processPayment($auction_id, $_SESSION['user_id']);
        
        // Update session balance
        if ($result['success']) {
            $user_sql = "SELECT balance FROM users WHERE id = ?";
            $stmt = $conn->prepare($user_sql);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $_SESSION['user_balance'] = $user['balance'];
            $stmt->close();
        }
        
        $conn->close();
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        header('Location: index.php?action=my-bids');
        exit;
    }
}

} // end if class_exists check
?>