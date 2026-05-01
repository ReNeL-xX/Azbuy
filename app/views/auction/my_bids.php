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
            <i class="fas fa-peso-sign"></i>
            <div>
                <span class="label">Total Spent</span>
                <span class="value">
                    ₱<?php 
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
                        <th style="text-align: left;">Item</th>
                        <th style="text-align: center;">Your Bid</th>
                        <th style="text-align: center;">Current Price</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Time Left</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($my_bids as $bid): ?>
                        <tr>
                            <td class="item-cell" style="text-align: left;">
                                <div class="item-info">
                                    <?php if (isset($bid['image_url']) && $bid['image_url']): ?>
                                        <img src="/AzBuy/public/<?php echo $bid['image_url']; ?>" alt="<?php echo htmlspecialchars($bid['title']); ?>">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/50x50/1a1a1a/ffd700?text=<?php echo urlencode(substr($bid['title'], 0, 10)); ?>" alt="<?php echo htmlspecialchars($bid['title']); ?>">
                                    <?php endif; ?>
                                    <div>
                                        <strong><?php echo htmlspecialchars($bid['title']); ?></strong>
                                        <small>Seller: <?php echo isset($bid['seller_id']) && $bid['seller_id'] == $_SESSION['user_id'] ? 'You' : 'Other'; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="price" style="text-align: center;">₱<?php echo number_format($bid['amount'], 2); ?></td>
                            <td class="price" style="text-align: center;">₱<?php echo number_format($bid['current_price'], 2); ?></td>
                            <td style="text-align: center;">
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
                            <td class="time-cell" style="text-align: center;" data-endtime="<?php echo $bid['end_time']; ?>">
                                <?php if ($bid['auction_status'] == 'active'): ?>
                                    <span class="time-left">Loading...</span>
                                <?php elseif ($bid['auction_status'] == 'payment_pending' && $bid['bid_status'] == 'won_payment'): ?>
                                    <span class="time-left pending">Payment due</span>
                                <?php else: ?>
                                    <span class="time-left ended"><?php echo date('M d, H:i', strtotime($bid['end_time'])); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="actions" style="text-align: center;">
                                <a href="index.php?action=view-auction&id=<?php echo $bid['auction_id']; ?>" class="btn-sm btn-primary">View</a>
                                
                                <?php if ($bid['bid_status'] == 'outbid' && $bid['auction_status'] == 'active'): ?>
                                    <a href="index.php?action=view-auction&id=<?php echo $bid['auction_id']; ?>" class="btn-sm btn-success">Place Higher Bid</a>
                                <?php endif; ?>
                                
                                <?php if ($bid['auction_status'] == 'active'): ?>
                                    <a href="javascript:void(0)" onclick="cancelBid(<?php echo $bid['id']; ?>, '<?php echo htmlspecialchars($bid['title']); ?>')" class="btn-sm btn-danger">Cancel Bid</a>
                                <?php endif; ?>
                                
                                <?php if ($bid['bid_status'] == 'won_payment'): ?>
                                    <button class="btn-sm btn-success pay-now-btn" data-auction-id="<?php echo $bid['auction_id']; ?>" data-amount="<?php echo $bid['amount']; ?>" data-title="<?php echo htmlspecialchars($bid['title']); ?>">Pay Now</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="payment-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Complete Purchase</h3>
            <button class="close-modal" onclick="closePaymentModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>You are about to purchase: <strong id="modalItemTitle"></strong></p>
            <p>Amount: <strong class="price" id="modalBidAmount"></strong></p>
            <p style="color: var(--text-muted); margin-top: 1rem;">Click confirm to complete your purchase. The seller will be notified immediately.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closePaymentModal()">Cancel</button>
            <button class="btn btn-primary" id="confirmPaymentBtn">Confirm Purchase</button>
        </div>
    </div>
</div>

<script>
let currentAuctionId = null;

// Use event delegation for Pay Now buttons
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers to all Pay Now buttons
    document.querySelectorAll('.pay-now-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const auctionId = this.dataset.auctionId;
            const amount = this.dataset.amount;
            const title = this.dataset.title;
            
            currentAuctionId = auctionId;
            
            document.getElementById('modalItemTitle').innerText = title;
            document.getElementById('modalBidAmount').innerHTML = '₱' + parseFloat(amount).toFixed(2);
            document.getElementById('paymentModal').style.display = 'flex';
        });
    });
    
    // Set confirm button handler
    const confirmBtn = document.getElementById('confirmPaymentBtn');
    if (confirmBtn) {
        confirmBtn.onclick = function() {
            if (!currentAuctionId) {
                alert('Please try again.');
                return;
            }
            
            // Show loading
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;
            
            // Send payment request
            fetch('index.php?action=pay-auction&id=' + currentAuctionId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✓ ' + data.message);
                        closePaymentModal();
                        location.reload();
                    } else {
                        alert('✗ ' + data.message);
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        };
    }
    
    // Update countdown timers
    updateCountdowns();
});

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
    currentAuctionId = null;
}

function cancelBid(bidId, itemTitle) {
    if (confirm('Are you sure you want to cancel your bid on "' + itemTitle + '"? This action cannot be undone.')) {
        window.location.href = 'index.php?action=cancel-bid&id=' + bidId;
    }
}

function updateCountdowns() {
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

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('paymentModal');
    if (event.target == modal) {
        closePaymentModal();
    }
}
</script>

<style>
.my-items-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.my-items-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.my-items-header h1 {
    color: var(--primary-gold);
    margin-bottom: 0.5rem;
}

.my-items-header p {
    color: var(--text-secondary);
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
    font-weight: 600;
    border-bottom: 1px solid var(--border-color);
}

.items-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-secondary);
    vertical-align: middle;
}

/* Remove border-bottom only from the actions column */
.items-table td.actions {
    border-bottom: none;
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
    justify-content: center;
}

/* Button styles */
.btn-sm {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none !important;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.3s;
    cursor: pointer;
    border: none;
}

.btn-sm.btn-primary {
    background: var(--primary-gold);
    color: #000;
    text-decoration: none !important;
}

.btn-sm.btn-success {
    background: #28a745;
    color: white;
    text-decoration: none !important;
}

.btn-sm.btn-danger {
    background: #dc3545;
    color: white;
    text-decoration: none !important;
}

.btn-sm:hover {
    transform: translateY(-2px);
    text-decoration: none !important;
}

/* Remove underline from all links in actions */
.actions a {
    text-decoration: none !important;
}

.actions a:hover {
    text-decoration: none !important;
}

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

.empty-state h3 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
}

/* Payment Modal */
.payment-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(5px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2000;
}

.modal-content {
    background: var(--dark-elevated);
    border-radius: 24px;
    max-width: 500px;
    width: 90%;
    border: 1px solid var(--primary-gold);
}

.modal-header {
    padding: 1.2rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    color: var(--primary-gold);
}

.close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--text-secondary);
    cursor: pointer;
}

.close-modal:hover {
    color: var(--primary-gold);
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1.2rem;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .my-items-container {
        padding: 1rem;
    }
    
    .my-items-header {
        flex-direction: column;
        text-align: center;
    }
    
    .summary-cards {
        grid-template-columns: 1fr;
    }
    
    .items-table th,
    .items-table td {
        padding: 0.75rem;
        font-size: 12px;
    }
    
    .item-cell .item-info img {
        width: 40px;
        height: 40px;
    }
    
    .actions {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>