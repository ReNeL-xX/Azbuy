<?php
class Auction {
    private mysqli $conn;
    
    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }
    
    public function create($seller_id, $title, $description, $category, $starting_price, $end_time, $image_url = null): array {
        if (empty($title) || $starting_price <= 0) {
            return ['success' => false, 'message' => 'Invalid auction data'];
        }
        
        $sql = "INSERT INTO auctions (seller_id, title, description, category, starting_price, current_price, end_time, image_url, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')";
        $stmt = $this->conn->prepare($sql);
        $current_price = $starting_price;
        $stmt->bind_param("isssddss", $seller_id, $title, $description, $category, $starting_price, $current_price, $end_time, $image_url);
        
        if ($stmt->execute()) {
            $auction_id = $this->conn->insert_id;
            $stmt->close();
            return ['success' => true, 'message' => 'Auction created successfully!', 'auction_id' => $auction_id];
        }
        
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to create auction: ' . $error];
    }
    
    public function getAllActive($limit = 20, $offset = 0, $category = null) {
        $sql = "SELECT a.*, u.username as seller_name, 
                (SELECT COUNT(*) FROM bids WHERE auction_id = a.id) as bid_count
                FROM auctions a 
                JOIN users u ON a.seller_id = u.id 
                WHERE a.status = 'active' AND a.end_time > NOW()";
        
        if ($category && $category != '') {
            $sql .= " AND a.category = ?";
        }
        
        $sql .= " ORDER BY a.end_time ASC LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        if ($category && $category != '') {
            $stmt->bind_param("sii", $category, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $auctions = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $auctions;
    }
    
    public function getAuctionById($id) {
        $sql = "SELECT a.*, u.username as seller_name, u.id as seller_id
                FROM auctions a 
                JOIN users u ON a.seller_id = u.id 
                WHERE a.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $auction = $result->fetch_assoc();
        $stmt->close();
        return $auction;
    }
    
    public function placeBid($auction_id, $bidder_id, $amount): array {
        $this->conn->begin_transaction();
        
        try {
            $check_sql = "SELECT current_price, end_time, status, seller_id FROM auctions WHERE id = ? FOR UPDATE";
            $stmt = $this->conn->prepare($check_sql);
            $stmt->bind_param("i", $auction_id);
            $stmt->execute();
            $auction = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            if (!$auction) {
                throw new Exception("Auction not found");
            }
            
            if ($auction['status'] != 'active') {
                throw new Exception("This auction is no longer active");
            }
            
            if (strtotime($auction['end_time']) < time()) {
                throw new Exception("Auction has already ended");
            }
            
            if ($bidder_id == $auction['seller_id']) {
                throw new Exception("You cannot bid on your own auction");
            }
            
            $min_bid = $auction['current_price'] + 1;
            if ($amount < $min_bid) {
                throw new Exception("Bid must be at least $" . number_format($min_bid, 2));
            }
            
            // Insert bid
            $bid_sql = "INSERT INTO bids (auction_id, bidder_id, amount) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($bid_sql);
            $stmt->bind_param("iid", $auction_id, $bidder_id, $amount);
            $stmt->execute();
            $stmt->close();
            
            // Update auction current price
            $update_sql = "UPDATE auctions SET current_price = ? WHERE id = ?";
            $stmt = $this->conn->prepare($update_sql);
            $stmt->bind_param("di", $amount, $auction_id);
            $stmt->execute();
            $stmt->close();
            
            $this->conn->commit();
            return ['success' => true, 'message' => 'Bid placed successfully!'];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function getUserAuctions($user_id) {
        $sql = "SELECT * FROM auctions WHERE seller_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $auctions = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $auctions;
    }
    
    public function getUserBids($user_id) {
        $sql = "SELECT b.*, a.title, a.end_time, a.status as auction_status, a.current_price, a.seller_id, a.image_url
                FROM bids b 
                JOIN auctions a ON b.auction_id = a.id 
                WHERE b.bidder_id = ? 
                ORDER BY b.bid_time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $bids = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        // Get highest bid for each auction to determine status
        foreach ($bids as &$bid) {
            $highest_sql = "SELECT MAX(amount) as max_bid FROM bids WHERE auction_id = ?";
            $stmt = $this->conn->prepare($highest_sql);
            $stmt->bind_param("i", $bid['auction_id']);
            $stmt->execute();
            $highest = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            $highest_bid = $highest['max_bid'] ?? 0;
            
            if ($bid['auction_status'] == 'active') {
                if ($bid['amount'] == $highest_bid) {
                    $bid['bid_status'] = 'winning';
                } else {
                    $bid['bid_status'] = 'outbid';
                }
            } elseif ($bid['auction_status'] == 'payment_pending' && $bid['amount'] == $highest_bid) {
                $bid['bid_status'] = 'won_payment';
            } elseif ($bid['auction_status'] == 'ended') {
                if ($bid['amount'] == $highest_bid) {
                    $bid['bid_status'] = 'won';
                } else {
                    $bid['bid_status'] = 'lost';
                }
            } else {
                $bid['bid_status'] = $bid['auction_status'];
            }
        }
        
        return $bids;
    }
    
    public function deleteAuction($auction_id, $user_id): array {
        $check_sql = "SELECT id, status FROM auctions WHERE id = ? AND seller_id = ?";
        $stmt = $this->conn->prepare($check_sql);
        $stmt->bind_param("ii", $auction_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $auction = $result->fetch_assoc();
        $stmt->close();
        
        if (!$auction) {
            return ['success' => false, 'message' => 'Auction not found'];
        }
        
        if ($auction['status'] != 'active') {
            return ['success' => false, 'message' => 'Only active auctions can be deleted'];
        }
        
        $sql = "DELETE FROM auctions WHERE id = ? AND seller_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $auction_id, $user_id);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Auction deleted successfully'];
        }
        
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to delete auction: ' . $error];
    }
    
    public function checkEndedAuctions() {
        $sql = "SELECT id FROM auctions WHERE status = 'active' AND end_time <= NOW()";
        $result = $this->conn->query($sql);
        $ended_auctions = $result->fetch_all(MYSQLI_ASSOC);
        
        foreach ($ended_auctions as $auction) {
            $highest_bid_sql = "SELECT bidder_id, amount FROM bids WHERE auction_id = ? ORDER BY amount DESC LIMIT 1";
            $stmt = $this->conn->prepare($highest_bid_sql);
            $stmt->bind_param("i", $auction['id']);
            $stmt->execute();
            $highest_bid = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            $payment_deadline = date('Y-m-d H:i:s', strtotime('+48 hours'));
            
            if ($highest_bid) {
                $update_sql = "UPDATE auctions SET status = 'payment_pending', winner_id = ?, winning_bid = ?, payment_deadline = ? WHERE id = ?";
                $stmt = $this->conn->prepare($update_sql);
                $stmt->bind_param("idsi", $highest_bid['bidder_id'], $highest_bid['amount'], $payment_deadline, $auction['id']);
                $stmt->execute();
                $stmt->close();
            } else {
                $update_sql = "UPDATE auctions SET status = 'ended' WHERE id = ?";
                $stmt = $this->conn->prepare($update_sql);
                $stmt->bind_param("i", $auction['id']);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
    
    public function processPayment($auction_id, $user_id): array {
        $this->conn->begin_transaction();
        
        try {
            $sql = "SELECT * FROM auctions WHERE id = ? AND winner_id = ? AND status = 'payment_pending' AND payment_deadline > NOW()";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $auction_id, $user_id);
            $stmt->execute();
            $auction = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            if (!$auction) {
                throw new Exception("Invalid auction or payment deadline passed");
            }
            
            $user_sql = "SELECT balance FROM users WHERE id = ? FOR UPDATE";
            $stmt = $this->conn->prepare($user_sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            if ($user['balance'] < $auction['winning_bid']) {
                throw new Exception("Insufficient balance");
            }
            
            $update_balance = "UPDATE users SET balance = balance - ? WHERE id = ?";
            $stmt = $this->conn->prepare($update_balance);
            $stmt->bind_param("di", $auction['winning_bid'], $user_id);
            $stmt->execute();
            $stmt->close();
            
            $update_auction = "UPDATE auctions SET status = 'paid' WHERE id = ?";
            $stmt = $this->conn->prepare($update_auction);
            $stmt->bind_param("i", $auction_id);
            $stmt->execute();
            $stmt->close();
            
            $update_seller = "UPDATE users SET balance = balance + ? WHERE id = ?";
            $stmt = $this->conn->prepare($update_seller);
            $stmt->bind_param("di", $auction['winning_bid'], $auction['seller_id']);
            $stmt->execute();
            $stmt->close();
            
            $this->conn->commit();
            return ['success' => true, 'message' => 'Payment successful!'];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>


