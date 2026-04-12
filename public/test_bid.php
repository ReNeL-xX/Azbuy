<?php
session_start();
require_once '../config/Database.php';

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please login first. <a href='index.php?action=login'>Login</a>";
    exit;
}

echo "<h1>Bidding Test</h1>";
echo "Logged in as: " . $_SESSION['username'] . " (ID: " . $_SESSION['user_id'] . ")<br><br>";

$database = new Database();
$conn = $database->connect();

// Get all active auctions
$sql = "SELECT id, title, current_price, seller_id FROM auctions WHERE status = 'active' AND end_time > NOW()";
$result = $conn->query($sql);
$auctions = $result->fetch_all(MYSQLI_ASSOC);

if (empty($auctions)) {
    echo "No active auctions found. <a href='index.php?action=create-auction'>Create an auction first</a>";
} else {
    echo "<h2>Available Auctions:</h2>";
    echo "<form method='POST' action=''>";
    echo "<select name='auction_id'>";
    foreach ($auctions as $auction) {
        echo "<option value='{$auction['id']}'>";
        echo $auction['title'] . " - Current: $" . $auction['current_price'];
        if ($auction['seller_id'] == $_SESSION['user_id']) {
            echo " (YOUR AUCTION - CAN'T BID)";
        }
        echo "</option>";
    }
    echo "</select><br><br>";
    echo "Bid Amount: $<input type='number' name='amount' step='1' required><br><br>";
    echo "<button type='submit' name='bid'>Place Bid</button>";
    echo "</form>";
}

// Handle bid submission
if (isset($_POST['bid'])) {
    $auction_id = intval($_POST['auction_id']);
    $amount = floatval($_POST['amount']);
    $user_id = $_SESSION['user_id'];
    
    echo "<h3>Attempting to place bid...</h3>";
    echo "Auction ID: $auction_id<br>";
    echo "User ID: $user_id<br>";
    echo "Amount: $$amount<br><br>";
    
    // Check auction
    $check = $conn->prepare("SELECT current_price, seller_id, status, end_time FROM auctions WHERE id = ?");
    $check->bind_param("i", $auction_id);
    $check->execute();
    $auction = $check->get_result()->fetch_assoc();
    
    if (!$auction) {
        echo "ERROR: Auction not found!";
    } elseif ($auction['status'] != 'active') {
        echo "ERROR: Auction is not active. Status: " . $auction['status'];
    } elseif (strtotime($auction['end_time']) < time()) {
        echo "ERROR: Auction has ended!";
    } elseif ($auction['seller_id'] == $user_id) {
        echo "ERROR: You cannot bid on your own auction!";
    } elseif ($amount <= $auction['current_price']) {
        echo "ERROR: Bid must be higher than current price of $" . $auction['current_price'];
    } else {
        // Try to insert bid
        $insert = $conn->prepare("INSERT INTO bids (auction_id, bidder_id, amount) VALUES (?, ?, ?)");
        $insert->bind_param("iid", $auction_id, $user_id, $amount);
        
        if ($insert->execute()) {
            echo "✓ BID INSERTED SUCCESSFULLY!<br>";
            
            // Update auction price
            $update = $conn->prepare("UPDATE auctions SET current_price = ? WHERE id = ?");
            $update->bind_param("di", $amount, $auction_id);
            $update->execute();
            
            echo "✓ Auction price updated to $$amount<br>";
        } else {
            echo "ERROR inserting bid: " . $insert->error;
        }
        $insert->close();
    }
    $check->close();
}

$conn->close();

echo "<br><br><a href='index.php?action=my-bids'>Go to My Bids/Activities</a>";
?>