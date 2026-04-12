<?php 
$page_title = "My Bidding Activity";

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

// Make sure $my_auctions is set
if (!isset($my_auctions)) {
    $my_auctions = [];
}

ob_start();
?>

<div class="my-items-container">
    <div class="my-items-header">
        <div>
            <h1><i class="fas fa-chart-line"></i> My Bidding Activity</h1>
            <p>Track all your bids and auction wins</p>
        </div>
        <a href="index.php?action=dashboard" class="btn btn-secondary">
            <i class="fas fa-search"></i> Browse More
        </a>
    </div>
    
    <div class="summary-cards">
        <div class="summary-card">
            <i class="fas fa-gavel"></i>
            <div>
                <span class="label">Total Bids Placed</span>
                <span class="value"><?php echo count($my_bids); ?></span>
            </div>
        </div>
        <div class="summary-card">
            <i class="fas fa-trophy"></i>
            <div>
                <span class="label">Auctions Won</span>
                <span class="value">
                    <?php 
                    $won_count = 0;
                    foreach ($my_bids as $bid) {
                        if ($bid['bid_status'] == 'won' || $bid['bid_status'] == 'won_payment') {
                            $won_count++;
                        }
                    }
                    echo $won_count;
                    ?>
                </span>
            </div>
        </div>
        <div class="summary-card">
            <i class="fas fa-dollar-sign"></i>
            <div>
                <span class="label">Total Spent</span>
                <span class="value">
                    $<?php 
                    $total_spent = 0;
                    foreach ($my_bids as $bid) {
                        if ($bid['bid_status'] == 'won' || $bid['bid_status'] == 'won_payment') {
                            $total_spent += $bid['amount'];
                        }
                    }
                    echo number_format($total_spent, 2);
                    ?>
                </span>
            </div>
        </div>
        <div class="summary-card">
            <i class="fas fa-clock"></i>
            <div>
                <span class="label">Active Bids</span>
                <span class="value">
                    <?php 
                    $active_count = 0;
                    foreach ($my_bids as $bid) {
                        if ($bid['auction_status'] == 'active') {
                            $active_count++;
                        }
                    }
                    echo $active_count;
                    ?>
                </span>
            </div>
        </div>
    </div>
    
    <?php if (empty($my_bids)): ?>
        <div class="empty-state">
            <i class="fas fa-gavel"></i>
            <h3>No Bids Yet</h3>
            <p>You haven't placed any bids yet. Start bidding now!</p>
            <a href="index.php?action=dashboard" class="btn btn-primary">Browse Auctions</a>
        </div>
    <?php else: ?>
        <div class="items-table">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Your Bid</th>
                        <th>Current Price</th>
                        <th>Status</th>
                        <th>Time Left</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($my_bids as $bid): ?>
                        <tr class="<?php echo $bid['bid_status'] == 'winning' ? 'winning-row' : ($bid['bid_status'] == 'outbid' ? 'active-row' : ($bid['bid_status'] == 'won_payment' ? 'payment-row' : 'lost-row')); ?>">
                            <td class="item-cell">
                                <div class="item-info">
                                    <?php if ($bid['image_url']): ?>
                                        <img src="/AzBuy/public/<?php echo $bid['image_url']; ?>" alt="<?php echo htmlspecialchars($bid['title']); ?>" onerror="this.src='https://via.placeholder.com/50x50/1a1a1a/ffd700?text=No+Image'">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/50x50/1a1a1a/ffd700?text=<?php echo urlencode(substr($bid['title'], 0, 10)); ?>" alt="<?php echo htmlspecialchars($bid['title']); ?>">
                                    <?php endif; ?>
                                    <div>
                                        <strong><?php echo htmlspecialchars($bid['title']); ?></strong>
                                        <small>Seller: <?php echo $bid['seller_id'] == $_SESSION['user_id'] ? 'You' : 'Other'; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="price">$<?php echo number_format($bid['amount'], 2); ?></td>
                            <td class="price">$<?php echo number_format($bid['current_price'], 2); ?></td>
                            <td>
                                <?php if ($bid['bid_status'] == 'winning'): ?>
                                    <span class="status-badge winning">You're Winning</span>
                                <?php elseif ($bid['bid_status'] == 'outbid'): ?>
                                    <span class="status-badge outbid">Outbid</span>
                                <?php elseif ($bid['bid_status'] == 'won_payment'): ?>
                                    <span class="status-badge won">Won - Payment Required</span>
                                <?php elseif ($bid['bid_status'] == 'won'): ?>
                                    <span class="status-badge won">Won - Paid</span>
                                <?php elseif ($bid['bid_status'] == 'lost'): ?>
                                    <span class="status-badge lost">Lost</span>
                                <?php else: ?>
                                    <span class="status-badge ended"><?php echo ucfirst($bid['auction_status']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($bid['auction_status'] == 'active'): ?>
                                    <?php
                                    $time_left = strtotime($bid['end_time']) - time();
                                    if ($time_left > 0):
                                        $hours = floor($time_left / 3600);
                                        $minutes = floor(($time_left % 3600) / 60);
                                    ?>
                                        <span class="countdown-small"><?php echo $hours; ?>h <?php echo $minutes; ?>m</span>
                                    <?php else: ?>
                                        <span>Ended</span>
                                    <?php endif; ?>
                                <?php elseif ($bid['bid_status'] == 'won_payment'): ?>
                                    <span>Payment due</span>
                                <?php else: ?>
                                    <span><?php echo date('M d, H:i', strtotime($bid['end_time'])); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a href="index.php?action=view-auction&id=<?php echo $bid['auction_id']; ?>" class="btn-sm btn-primary">View</a>
                                <?php if ($bid['bid_status'] == 'outbid' && $bid['auction_status'] == 'active'): ?>
                                    <a href="index.php?action=view-auction&id=<?php echo $bid['auction_id']; ?>" class="btn-sm btn-success">Place Higher Bid</a>
                                <?php endif; ?>
                                <?php if ($bid['bid_status'] == 'won_payment'): ?>
                                    <button class="btn-sm btn-success" onclick="openPaymentModal(<?php echo $bid['auction_id']; ?>, <?php echo $bid['amount']; ?>)">Pay Now</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Your Active Listings Section -->
    <?php if (!empty($my_auctions)): ?>
    <div class="my-auctions-section" style="margin-top: 3rem;">
        <h2><i class="fas fa-store"></i> Your Active Listings</h2>
        <div class="auctions-grid" style="margin-top: 1rem;">
            <?php 
            $active_my_auctions = array_filter($my_auctions, function($a) { 
                return $a['status'] == 'active'; 
            });
            ?>
            <?php if (empty($active_my_auctions)): ?>
                <div class="empty-state" style="grid-column: 1/-1; padding: 2rem;">
                    <p>No active listings. <a href="index.php?action=create-auction">Create an auction</a> to start selling!</p>
                </div>
            <?php else: ?>
                <?php foreach (array_slice($active_my_auctions, 0, 4) as $auction): ?>
                    <div class="auction-card">
                        <div class="auction-image">
                            <?php if ($auction['image_url']): ?>
                                <img src="/AzBuy/public/<?php echo $auction['image_url']; ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>" onerror="this.src='https://via.placeholder.com/300x200/1a1a1a/ffd700?text=No+Image'">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300x200/1a1a1a/ffd700?text=<?php echo urlencode($auction['title']); ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>">
                            <?php endif; ?>
                            <div class="auction-timer">
                                <i class="fas fa-hourglass-half"></i> 
                                <span class="countdown" data-endtime="<?php echo $auction['end_time']; ?>"></span>
                            </div>
                        </div>
                        <div class="auction-info">
                            <h3><?php echo htmlspecialchars($auction['title']); ?></h3>
                            <p class="category"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($auction['category']); ?></p>
                            <div class="price-info">
                                <span class="current-price">$<?php echo number_format($auction['current_price'], 2); ?></span>
                                <span class="starting-price">Started: $<?php echo number_format($auction['starting_price'], 2); ?></span>
                            </div>
                            <a href="index.php?action=view-auction&id=<?php echo $auction['id']; ?>" class="btn btn-primary btn-block">View Auction</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php if (count($active_my_auctions) > 4): ?>
            <div style="text-align: center; margin-top: 1rem;">
                <a href="index.php?action=my-auctions" class="btn btn-secondary">View All Your Auctions →</a>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="payment-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Complete Payment</h3>
            <button class="close-modal" onclick="closePaymentModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>You won: <strong id="modalItemTitle"></strong></p>
            <p>Winning bid: <strong class="price" id="modalBidAmount"></strong></p>
            <p>Your balance: <strong class="price" id="modalUserBalance">$<?php echo number_format($_SESSION['user_balance'] ?? 0, 2); ?></strong></p>
            <div class="payment-methods">
                <label><input type="radio" name="payment" value="balance" checked> Use Wallet Balance</label>
                <label><input type="radio" name="payment" value="card"> Credit/Debit Card</label>
                <label><input type="radio" name="payment" value="paypal"> PayPal</label>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closePaymentModal()">Cancel</button>
            <button class="btn btn-primary" onclick="confirmPayment()">Confirm Payment</button>
        </div>
    </div>
</div>

<script>
let currentAuctionId = null;
let currentBidAmount = 0;

function openPaymentModal(auctionId, amount) {
    currentAuctionId = auctionId;
    currentBidAmount = amount;
    
    // Find the item title from the row
    const row = event.target.closest('tr');
    const itemTitle = row.querySelector('.item-info strong').innerText;
    
    document.getElementById('modalItemTitle').innerText = itemTitle;
    document.getElementById('modalBidAmount').innerHTML = '$' + amount.toFixed(2);
    document.getElementById('paymentModal').style.display = 'flex';
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
    currentAuctionId = null;
    currentBidAmount = 0;
}

function confirmPayment() {
    if (currentAuctionId) {
        window.location.href = 'index.php?action=pay-auction&id=' + currentAuctionId;
    }
}

// Countdown timers for active listings
function initCountdowns() {
    const countdowns = document.querySelectorAll('.countdown');
    countdowns.forEach(el => {
        const endTime = new Date(el.dataset.endtime).getTime();
        
        function update() {
            const now = new Date().getTime();
            const distance = endTime - now;
            
            if (distance < 0) {
                el.innerHTML = "Ended";
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            let display = '';
            if (days > 0) display += days + 'd ';
            display += hours + 'h ' + minutes + 'm';
            el.innerHTML = display;
        }
        
        update();
        setInterval(update, 1000);
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('paymentModal');
    if (event.target == modal) {
        closePaymentModal();
    }
}

initCountdowns();
</script>

<style>
.empty-state {
    text-align: center;
    padding: 4rem;
    background: var(--dark-elevated);
    border-radius: 20px;
}
.empty-state i {
    font-size: 4rem;
    color: var(--primary-gold);
    margin-bottom: 1rem;
}
.btn-sm {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.3s;
}
.btn-sm.btn-primary {
    background: var(--primary-gold);
    color: #000;
}
.btn-sm.btn-success {
    background: #28a745;
    color: white;
}
.btn-sm.btn-primary:hover, .btn-sm.btn-success:hover {
    transform: translateY(-2px);
}
.my-auctions-section h2 {
    color: var(--primary-gold);
    margin-bottom: 1rem;
    font-size: 1.5rem;
}
.auctions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>