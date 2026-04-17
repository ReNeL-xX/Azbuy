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
        <div class="table-responsive">
            <table class="items-table">
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
                            <td class="time-cell" data-endtime="<?php echo $bid['end_time']; ?>">
                                <?php if ($bid['auction_status'] == 'active'): ?>
                                    <span class="time-left">Loading...</span>
                                <?php elseif ($bid['auction_status'] == 'payment_pending' && $bid['bid_status'] == 'won_payment'): ?>
                                    <span class="time-left pending">Payment due</span>
                                <?php else: ?>
                                    <span class="time-left ended"><?php echo date('M d, H:i', strtotime($bid['end_time'])); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a href="index.php?action=view-auction&id=<?php echo $bid['auction_id']; ?>" class="btn-sm btn-primary">View</a>
                                
                                <?php if ($bid['bid_status'] == 'outbid' && $bid['auction_status'] == 'active'): ?>
                                    <a href="index.php?action=view-auction&id=<?php echo $bid['auction_id']; ?>" class="btn-sm btn-success">Place Higher Bid</a>
                                <?php endif; ?>
                                
                                <!-- Cancel Bid button for ANY active bid (winning OR outbid) -->
                                <?php if ($bid['auction_status'] == 'active'): ?>
                                    <a href="javascript:void(0)" onclick="cancelBid(<?php echo $bid['id']; ?>, '<?php echo htmlspecialchars($bid['title']); ?>')" class="btn-sm btn-danger">Cancel Bid</a>
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

function cancelBid(bidId, itemTitle) {
    if (confirm('Are you sure you want to cancel your bid on "' + itemTitle + '"? This action cannot be undone.')) {
        window.location.href = 'index.php?action=cancel-bid&id=' + bidId;
    }
}

// Update countdown timers on my bids page (matches dashboard)
function updateBidCountdowns() {
    const timeCells = document.querySelectorAll('.time-cell[data-endtime]');
    
    timeCells.forEach(cell => {
        const endTimeStr = cell.dataset.endtime;
        if (!endTimeStr) return;
        
        const endTime = new Date(endTimeStr).getTime();
        const timeSpan = cell.querySelector('.time-left');
        
        if (!timeSpan) return;
        
        function update() {
            const now = new Date().getTime();
            const distance = endTime - now;
            
            if (distance < 0) {
                timeSpan.innerHTML = 'Ended';
                timeSpan.classList.add('ended');
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            
            let display = '';
            if (days > 0) display += days + 'd ';
            display += hours + 'h ' + minutes + 'm';
            timeSpan.innerHTML = display;
            timeSpan.classList.remove('ended');
        }
        
        update();
        setInterval(update, 60000);
    });
}

// Countdown timers for active listings section
function initCountdowns() {
    const countdowns = document.querySelectorAll('.countdown');
    countdowns.forEach(el => {
        const endTimeStr = el.dataset.endtime;
        if (!endTimeStr) return;
        
        const endTime = new Date(endTimeStr).getTime();
        
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
            
            let display = '';
            if (days > 0) display += days + 'd ';
            display += hours + 'h ' + minutes + 'm';
            el.innerHTML = display;
        }
        
        update();
        setInterval(update, 60000);
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('paymentModal');
    if (event.target == modal) {
        closePaymentModal();
    }
}

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateBidCountdowns();
    initCountdowns();
});
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
.btn-sm.btn-danger {
    background: #dc3545;
    color: white;
}
.btn-sm.btn-primary:hover, .btn-sm.btn-success:hover, .btn-sm.btn-danger:hover {
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
.table-responsive {
    overflow-x: auto;
    background: var(--dark-elevated);
    border-radius: 20px;
    border: 1px solid var(--border-color);
}
.items-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}
.items-table th {
    background: rgba(255, 215, 0, 0.1);
    color: var(--primary-gold);
    padding: 1rem;
    text-align: left;
    font-weight: 600;
}
.items-table td {
    padding: 1rem;
    border-bottom: none;
    color: var(--text-secondary);
}
.items-table tr:hover {
    background: rgba(255, 215, 0, 0.05);
}
.item-cell .item-info {
    display: flex;
    align-items: center;
    gap: 12px;
}
.item-cell .item-info img {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    object-fit: cover;
}
.item-cell .item-info strong {
    display: block;
    color: var(--text-primary);
}
.item-cell .item-info small {
    font-size: 11px;
    color: var(--text-muted);
}
.price {
    font-weight: 700;
    color: var(--primary-gold);
}
.time-left {
    font-size: 13px;
    color: var(--text-secondary);
}
.time-left.ended {
    color: #dc3545;
}
.time-left.pending {
    color: #ffc107;
}
.time-cell {
    white-space: nowrap;
}
.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
}
.status-badge.winning {
    background: rgba(40, 167, 69, 0.15);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}
.status-badge.outbid {
    background: rgba(220, 53, 69, 0.15);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.3);
}
.status-badge.won {
    background: rgba(23, 162, 184, 0.15);
    color: #17a2b8;
    border: 1px solid rgba(23, 162, 184, 0.3);
}
.status-badge.lost {
    background: rgba(108, 117, 125, 0.15);
    color: #6c757d;
    border: 1px solid rgba(108, 117, 125, 0.3);
}
.status-badge.ended {
    background: rgba(108, 117, 125, 0.15);
    color: #6c757d;
    border: 1px solid rgba(108, 117, 125, 0.3);
}
.actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}
.summary-card {
    background: var(--dark-elevated);
    padding: 1rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid var(--border-color);
}
.summary-card i {
    font-size: 2rem;
    color: var(--primary-gold);
}
.summary-card .label {
    font-size: 12px;
    color: var(--text-muted);
    display: block;
}
.summary-card .value {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--primary-gold);
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>