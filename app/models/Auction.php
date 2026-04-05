<?php
class Auction {
    private mysqli $conn;
    
    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }
    
    public function create($seller_id, $title, $description, $category, $starting_price, $end_time, $image_url = null) {
        $sql = "INSERT INTO auctions (seller_id, title, description, category, starting_price, current_price, end_time, image_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $current_price = $starting_price;
        $stmt->bind_param("isssdds", $seller_id, $title, $description, $category, $starting_price, $current_price, $end_time, $image_url);
        return $stmt->execute();
    }
    
    public function getAllActive($limit = 20, $offset = 0, $category = null) {
        $sql = "SELECT a.*, u.username as seller_name, 
                (SELECT COUNT(*) FROM bids WHERE auction_id = a.id) as bid_count
                FROM auctions a 
                JOIN users u ON a.seller_id = u.id 
                WHERE a.status = 'active' AND a.end_time > NOW()";
        
        if ($category) {
            $sql .= " AND a.category = ?";
        }
        
        $sql .= " ORDER BY a.end_time ASC LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        if ($category) {
            $stmt->bind_param("sii", $category, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
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
        return $result->fetch_assoc();
    }
    
    public function placeBid($auction_id, $bidder_id, $amount) {
        $this->conn->begin_transaction();
        
        try {
            // Check if bid is higher than current price
            $check_sql = "SELECT current_price, end_time, status FROM auctions WHERE id = ? FOR UPDATE";
            $stmt = $this->conn->prepare($check_sql);
            $stmt->bind_param("i", $auction_id);
            $stmt->execute();
            $auction = $stmt->get_result()->fetch_assoc();
            
            if ($auction['status'] != 'active' || strtotime($auction['end_time']) < time()) {
                throw new Exception("Auction has ended");
            }
            
            if ($amount <= $auction['current_price']) {
                throw new Exception("Bid must be higher than current price");
            }
            
            // Insert bid
            $bid_sql = "INSERT INTO bids (auction_id, bidder_id, amount) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($bid_sql);
            $stmt->bind_param("iid", $auction_id, $bidder_id, $amount);
            $stmt->execute();
            
            // Update auction current price
            $update_sql = "UPDATE auctions SET current_price = ? WHERE id = ?";
            $stmt = $this->conn->prepare($update_sql);
            $stmt->bind_param("di", $amount, $auction_id);
            $stmt->execute();
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    
    public function getUserAuctions($user_id) {
        $sql = "SELECT * FROM auctions WHERE seller_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getUserBids($user_id) {
        $sql = "SELECT b.*, a.title, a.end_time, a.status as auction_status 
                FROM bids b 
                JOIN auctions a ON b.auction_id = a.id 
                WHERE b.bidder_id = ? 
                ORDER BY b.bid_time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function deleteAuction($auction_id, $user_id) {
        $sql = "DELETE FROM auctions WHERE id = ? AND seller_id = ? AND status = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $auction_id, $user_id);
        return $stmt->execute();
    }
    
    public function checkEndedAuctions() {
        $sql = "SELECT a.*, u.email as winner_email, u.username as winner_name 
                FROM auctions a 
                LEFT JOIN bids b ON a.id = b.auction_id 
                LEFT JOIN users u ON b.bidder_id = u.id
                WHERE a.status = 'active' AND a.end_time <= NOW()";
        
        $result = $this->conn->query($sql);
        $ended_auctions = $result->fetch_all(MYSQLI_ASSOC);
        
        foreach ($ended_auctions as $auction) {
            // Get highest bid
            $highest_bid_sql = "SELECT bidder_id, amount FROM bids WHERE auction_id = ? ORDER BY amount DESC LIMIT 1";
            $stmt = $this->conn->prepare($highest_bid_sql);
            $stmt->bind_param("i", $auction['id']);
            $stmt->execute();
            $highest_bid = $stmt->get_result()->fetch_assoc();
            
            $payment_deadline = date('Y-m-d H:i:s', strtotime('+48 hours'));
            
            if ($highest_bid) {
                $update_sql = "UPDATE auctions SET status = 'payment_pending', winner_id = ?, winning_bid = ?, payment_deadline = ? WHERE id = ?";
                $stmt = $this->conn->prepare($update_sql);
                $stmt->bind_param("idsi", $highest_bid['bidder_id'], $highest_bid['amount'], $payment_deadline, $auction['id']);
                $stmt->execute();
                
                // Notify winner
                $this->addNotification($highest_bid['bidder_id'], 'auction_won', 'You won an auction!', "You won {$auction['title']} with a bid of $" . $highest_bid['amount']);
            } else {
                $update_sql = "UPDATE auctions SET status = 'ended' WHERE id = ?";
                $stmt = $this->conn->prepare($update_sql);
                $stmt->bind_param("i", $auction['id']);
                $stmt->execute();
            }
        }
    }
    
    public function processPayment($auction_id, $user_id) {
        $this->conn->begin_transaction();
        
        try {
            $sql = "SELECT * FROM auctions WHERE id = ? AND winner_id = ? AND status = 'payment_pending' AND payment_deadline > NOW()";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $auction_id, $user_id);
            $stmt->execute();
            $auction = $stmt->get_result()->fetch_assoc();
            
            if (!$auction) {
                throw new Exception("Invalid auction or payment deadline passed");
            }
            
            // Check user balance
            $user_sql = "SELECT balance FROM users WHERE id = ? FOR UPDATE";
            $stmt = $this->conn->prepare($user_sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            
            if ($user['balance'] < $auction['winning_bid']) {
                throw new Exception("Insufficient balance");
            }
            
            // Deduct balance
            $update_balance = "UPDATE users SET balance = balance - ? WHERE id = ?";
            $stmt = $this->conn->prepare($update_balance);
            $stmt->bind_param("di", $auction['winning_bid'], $user_id);
            $stmt->execute();
            
            // Update auction status
            $update_auction = "UPDATE auctions SET status = 'paid' WHERE id = ?";
            $stmt = $this->conn->prepare($update_auction);
            $stmt->bind_param("i", $auction_id);
            $stmt->execute();
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    
    private function addNotification($user_id, $type, $title, $message) {
        $sql = "INSERT INTO notifications (user_id, type, title, message) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isss", $user_id, $type, $title, $message);
        $stmt->execute();
    }
}