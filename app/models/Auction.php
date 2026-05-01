<?php
class Auction {
    private mysqli $conn;
    
    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }
    
    // Create a new auction
    public function create($seller_id, $title, $description, $category, $starting_price, $end_time, $image_url = null, $bid_increment = 1.00): array {
        if (empty($title) || empty($starting_price) || $starting_price <= 0) {
            return ['success' => false, 'message' => 'Invalid auction data'];
        }
        
        $sql = "INSERT INTO auctions (seller_id, title, description, category, starting_price, current_price, bid_increment, end_time, image_url, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";
        $stmt = $this->conn->prepare($sql);
        $current_price = $starting_price;
        $stmt->bind_param("isssddsss", $seller_id, $title, $description, $category, $starting_price, $current_price, $bid_increment, $end_time, $image_url);
        
        if ($stmt->execute()) {
            $auction_id = $this->conn->insert_id;
            $stmt->close();
            return ['success' => true, 'message' => 'Auction created successfully!', 'auction_id' => $auction_id];
        }
        
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to create auction: ' . $error];
    }
    
    // Get all active auctions
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
    
    // Get auction by ID
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
    
    
    // Place a bid (automatically deletes previous bid from same user on this auction)
// Place a bid (automatically deletes previous bid from same user on this auction)
public function placeBid($auction_id, $bidder_id, $amount): array {
    $this->conn->begin_transaction();
    
    try {
        // Check auction status, current price, and bid increment
        $check_sql = "SELECT current_price, end_time, status, seller_id, bid_increment FROM auctions WHERE id = ? FOR UPDATE";
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
        
        // Calculate minimum allowed bid based on bid increment
        $bid_increment = $auction['bid_increment'];
        $current_price = $auction['current_price'];
        $min_bid = $current_price + $bid_increment;
        
        // Check if bid meets the minimum increment requirement (must be at least current_price + increment)
        if ($amount < $min_bid) {
throw new Exception("Bid must be at least ₱" . number_format($min_bid, 2) . " (Current price: ₱" . number_format($current_price, 2) . " + minimum increment: ₱" . number_format($bid_increment, 2) . ")");
        }
        
        // Note: User can bid ANY amount ABOVE the minimum. No multiple restriction.
        // Example: If current price is $100 and increment is $5, user can bid $105, $110, $150, $200, etc.
        
        // Delete user's previous bid on this auction (if exists)
        $delete_previous_sql = "DELETE FROM bids WHERE auction_id = ? AND bidder_id = ?";
        $stmt = $this->conn->prepare($delete_previous_sql);
        $stmt->bind_param("ii", $auction_id, $bidder_id);
        $stmt->execute();
        $stmt->close();
        
        // Insert new bid
        $bid_sql = "INSERT INTO bids (auction_id, bidder_id, amount) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($bid_sql);
        $stmt->bind_param("iid", $auction_id, $bidder_id, $amount);
        $stmt->execute();
        $stmt->close();
        
        // Get the new highest bid for this auction
        $highest_sql = "SELECT MAX(amount) as max_bid FROM bids WHERE auction_id = ?";
        $stmt = $this->conn->prepare($highest_sql);
        $stmt->bind_param("i", $auction_id);
        $stmt->execute();
        $highest = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        $new_current_price = $highest['max_bid'] ?? $current_price;
        
        // Update auction current price to the highest bid
        $update_sql = "UPDATE auctions SET current_price = ? WHERE id = ?";
        $stmt = $this->conn->prepare($update_sql);
        $stmt->bind_param("di", $new_current_price, $auction_id);
        $stmt->execute();
        $stmt->close();
        
        $this->conn->commit();
        return ['success' => true, 'message' => 'Bid placed successfully!'];
        
    } catch (Exception $e) {
        $this->conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
    
    // Get user's auctions
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
    
    // Get user's bids with auction details for activities page
    // Get user's bids with auction details for activities page (shows only latest bid per auction)
public function getUserBids($user_id) {
    $sql = "SELECT b.*, a.id as auction_id, a.title, a.end_time, a.status as auction_status, a.current_price, a.seller_id, a.image_url
            FROM bids b 
            INNER JOIN (
                SELECT auction_id, MAX(bid_time) as latest_bid_time
                FROM bids
                WHERE bidder_id = ?
                GROUP BY auction_id
            ) b2 ON b.auction_id = b2.auction_id AND b.bid_time = b2.latest_bid_time
            JOIN auctions a ON b.auction_id = a.id
            WHERE b.bidder_id = ?
            ORDER BY b.bid_time DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $user_id);
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
    
    // Delete auction
    public function deleteAuction($auction_id, $user_id): array {
        $check_sql = "SELECT id, status FROM auctions WHERE id = ? AND seller_id = ?";
        $stmt = $this->conn->prepare($check_sql);
        $stmt->bind_param("ii", $auction_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $auction = $result->fetch_assoc();
        $stmt->close();
        
        if (!$auction) {
            return ['success' => false, 'message' => 'Auction not found or you do not have permission'];
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
    
    // Delete/Cancel a bid (user can delete their own bid if auction is still active)
    public function deleteBid($bid_id, $user_id): array {
        // Check if bid exists and belongs to user
        $check_sql = "SELECT b.*, a.current_price, a.status, a.end_time, a.seller_id, a.starting_price
                      FROM bids b 
                      JOIN auctions a ON b.auction_id = a.id 
                      WHERE b.id = ? AND b.bidder_id = ?";
        $stmt = $this->conn->prepare($check_sql);
        $stmt->bind_param("ii", $bid_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $bid = $result->fetch_assoc();
        $stmt->close();
        
        if (!$bid) {
            return ['success' => false, 'message' => 'Bid not found or you do not have permission'];
        }
        
        // Check if auction is still active (can cancel any bid on active auction)
        if ($bid['status'] != 'active') {
            return ['success' => false, 'message' => 'Cannot cancel bid on ended auction'];
        }
        
        if (strtotime($bid['end_time']) < time()) {
            return ['success' => false, 'message' => 'Auction has already ended'];
        }
        
        // Start transaction
        $this->conn->begin_transaction();
        
        try {
            // Delete the bid
            $delete_sql = "DELETE FROM bids WHERE id = ?";
            $stmt = $this->conn->prepare($delete_sql);
            $stmt->bind_param("i", $bid_id);
            $stmt->execute();
            $stmt->close();
            
            // Get the new highest bid for this auction
            $highest_sql = "SELECT MAX(amount) as max_bid FROM bids WHERE auction_id = ?";
            $stmt = $this->conn->prepare($highest_sql);
            $stmt->bind_param("i", $bid['auction_id']);
            $stmt->execute();
            $highest = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            $new_current_price = $highest['max_bid'] ?? null;
            
            // If no bids left, revert to starting price
            if ($new_current_price === null) {
                $new_current_price = $bid['starting_price'];
            }
            
            // Update auction current price
            $update_sql = "UPDATE auctions SET current_price = ? WHERE id = ?";
            $stmt = $this->conn->prepare($update_sql);
            $stmt->bind_param("di", $new_current_price, $bid['auction_id']);
            $stmt->execute();
            $stmt->close();
            
            $this->conn->commit();
            return ['success' => true, 'message' => 'Bid cancelled successfully'];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => 'Failed to cancel bid: ' . $e->getMessage()];
        }
    }
    
    // Get single bid by ID (for verification)
    public function getBidById($bid_id, $user_id) {
        $sql = "SELECT b.*, a.title, a.status, a.end_time, a.current_price 
                FROM bids b 
                JOIN auctions a ON b.auction_id = a.id 
                WHERE b.id = ? AND b.bidder_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $bid_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $bid = $result->fetch_assoc();
        $stmt->close();
        return $bid;
    }
    
    // Update auction (only for active auctions that belong to the user)
    public function updateAuction($auction_id, $user_id, $title, $description, $category, $end_time, $image_url = null): array {
        // Check if auction exists and belongs to user
        $check_sql = "SELECT id, status, image_url FROM auctions WHERE id = ? AND seller_id = ?";
        $stmt = $this->conn->prepare($check_sql);
        $stmt->bind_param("ii", $auction_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $auction = $result->fetch_assoc();
        $stmt->close();
        
        if (!$auction) {
            return ['success' => false, 'message' => 'Auction not found or you do not have permission'];
        }
        
        if ($auction['status'] != 'active') {
            return ['success' => false, 'message' => 'Only active auctions can be edited'];
        }
        
        // Build update query
        $sql = "UPDATE auctions SET title = ?, description = ?, category = ?, end_time = ?";
        $params = [$title, $description, $category, $end_time];
        $types = "ssss";
        
        if ($image_url !== null) {
            $sql .= ", image_url = ?";
            $params[] = $image_url;
            $types .= "s";
        }
        
        $sql .= " WHERE id = ? AND seller_id = ?";
        $params[] = $auction_id;
        $params[] = $user_id;
        $types .= "ii";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Auction updated successfully'];
        }
        
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to update auction: ' . $error];
    }
    
    // Get auction for editing (verify ownership)
    public function getAuctionForEdit($auction_id, $user_id) {
        $sql = "SELECT * FROM auctions WHERE id = ? AND seller_id = ? AND status = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $auction_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $auction = $result->fetch_assoc();
        $stmt->close();
        return $auction;
    }
    
    // Check and update ended auctions
    public function checkEndedAuctions() {
        $sql = "SELECT id FROM auctions WHERE status = 'active' AND end_time <= NOW()";
        $result = $this->conn->query($sql);
        $ended_auctions = $result->fetch_all(MYSQLI_ASSOC);
        
        foreach ($ended_auctions as $auction) {
            // Get highest bid
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
    
// Process payment for won auction (simplified - no wallet check)
public function processPayment($auction_id, $user_id): array {
    $this->conn->begin_transaction();
    
    try {
        $sql = "SELECT a.*, u.username as seller_name 
                FROM auctions a 
                JOIN users u ON a.seller_id = u.id 
                WHERE a.id = ? AND a.winner_id = ? AND a.status = 'payment_pending'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $auction_id, $user_id);
        $stmt->execute();
        $auction = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$auction) {
            throw new Exception("Invalid auction or payment already processed");
        }
        
        // Get buyer info
        $buyer_sql = "SELECT username FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($buyer_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $buyer = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        // Create notification for seller
        $notification_title = "🎉 Item Sold!";
$notification_message = "Your item '{$auction['title']}' has been purchased by {$buyer['username']} for ₱" . number_format($auction['winning_bid'], 2) . "!";
        $this->addNotification($auction['seller_id'], 'item_sold', $notification_title, $notification_message);
        
        // Create notification for buyer
        $buyer_notification_title = "✅ Purchase Successful!";
        $buyer_notification_message = "You have successfully purchased '{$auction['title']}' for ₱" . number_format($auction['winning_bid'], 2) . ". The seller will contact you soon.";
        $this->addNotification($user_id, 'purchase_success', $buyer_notification_title, $buyer_notification_message);
        
        // Delete all bids for this auction
        $delete_bids_sql = "DELETE FROM bids WHERE auction_id = ?";
        $stmt = $this->conn->prepare($delete_bids_sql);
        $stmt->bind_param("i", $auction_id);
        $stmt->execute();
        $stmt->close();
        
        // Delete the auction
        $delete_auction_sql = "DELETE FROM auctions WHERE id = ?";
        $stmt = $this->conn->prepare($delete_auction_sql);
        $stmt->bind_param("i", $auction_id);
        $stmt->execute();
        $stmt->close();
        
        $this->conn->commit();
        return ['success' => true, 'message' => 'Payment successful! The item has been purchased. The auction has been removed.'];
        
    } catch (Exception $e) {
        $this->conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Add notification method
private function addNotification($user_id, $type, $title, $message) {
    $sql = "INSERT INTO notifications (user_id, type, title, message, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $type, $title, $message);
    $stmt->execute();
    $stmt->close();
}

// Get user notifications
public function getNotifications($user_id, $limit = 20) {
    $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $notifications;
}

// Get unread notification count
public function getUnreadNotificationCount($user_id) {
    $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    $stmt->close();
    return $count;
}

// Mark notification as read
public function markNotificationRead($notification_id, $user_id) {
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ii", $notification_id, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}
// Delete a single notification
public function deleteNotification($notification_id, $user_id): bool {
    $sql = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ii", $notification_id, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Delete all notifications for a user
public function deleteAllNotifications($user_id): bool {
    $sql = "DELETE FROM notifications WHERE user_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}
}
?>