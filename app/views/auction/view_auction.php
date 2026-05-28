<?php 
$page_title = $auction['title'];

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

ob_start();
?>

<div class="auction-detail">
    <div class="auction-detail-grid">
        <div class="auction-image-section">
            <div class="main-image">
                <?php if ($auction['image_url']): ?>
                    <img src="/AzBuy/public/<?php echo $auction['image_url']; ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>">
                <?php else: ?>
                    <img src="https://via.placeholder.com/600x400/1a1a1a/ffd700?text=<?php echo urlencode($auction['title']); ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>">
                <?php endif; ?>
            </div>
        </div>
        
        <div class="auction-info-section">
            <div class="auction-title">
                <h1><?php echo htmlspecialchars($auction['title']); ?></h1>
                <div class="seller-info">
                    <i class="fas fa-store"></i> Sold by 
                    <a href="index.php?action=user-profile&id=<?php echo $auction['seller_id']; ?>" class="seller-link">
                        <strong><?php echo htmlspecialchars($auction['seller_name']); ?></strong>
                    </a>
                </div>
            </div>
            
            <div class="bid-status-card">
                <div class="current-bid-section">
                    <span class="label">Current Bid</span>
                    <div class="current-bid-amount">₱<?php echo number_format($auction['current_price'], 2); ?></div>
                    <span class="bid-count"><?php echo count($bid_history); ?> bids</span>
                </div>
                
                <div class="time-left-section">
                    <span class="label"><i class="fas fa-hourglass-half"></i> Time Left</span>
                    <div class="countdown-large" data-endtime="<?php echo $auction['end_time']; ?>">
                        <span class="timer"></span>
                    </div>
                </div>
                
                <?php if ($_SESSION['user_id'] != $auction['seller_id'] && $auction['status'] == 'active' && strtotime($auction['end_time']) > time()): ?>
                <div class="bid-form-section">
                    <label>Your Bid (PHP)</label>
                    <div class="bid-input-group">
                        <span class="currency">₱</span>
                        <input type="number" id="bid_amount" step="<?php echo $auction['bid_increment']; ?>" value="<?php echo $auction['current_price'] + $auction['bid_increment']; ?>">
                        <button onclick="placeBid()" class="btn btn-primary">Place Bid</button>
                    </div>
                    <div class="bid-info">
                        <p><i class="fas fa-info-circle"></i> Minimum bid: <span id="minBid">₱<?php echo number_format($auction['current_price'] + $auction['bid_increment'], 2); ?></span></p>
                        <p><i class="fas fa-chart-line"></i> Bid increment: ₱<?php echo number_format($auction['bid_increment'], 2); ?></p>
                    </div>
                </div>
                <?php elseif ($_SESSION['user_id'] == $auction['seller_id']): ?>
                    <div class="alert alert-info">You cannot bid on your own auction</div>
                <?php elseif ($auction['status'] != 'active'): ?>
                    <div class="alert alert-error">This auction has ended</div>
                <?php endif; ?>
            </div>
            
            <div class="auction-details-tabs">
                <div class="tabs">
                    <button class="tab-btn active" onclick="showTab('description')">Description</button>
                    <button class="tab-btn" onclick="showTab('details')">Item Details</button>
                    <button class="tab-btn" onclick="showTab('shipping')">Shipping & Contact</button>
                    <button class="tab-btn" onclick="showTab('bids')">Bid History</button>
                </div>
                
                <div id="description" class="tab-content active">
                    <p><?php echo nl2br(htmlspecialchars($auction['description'] ?? 'No description provided.')); ?></p>
                </div>
                
                <div id="details" class="tab-content">
                    <table class="details-table">
                        <tr><th>Category</th><td><?php echo htmlspecialchars($auction['category']); ?></td></tr>
                        <tr><th>Starting Price</th><td>₱<?php echo number_format($auction['starting_price'], 2); ?></td></tr>
                        <tr><th>Current Price</th><td>₱<?php echo number_format($auction['current_price'], 2); ?></td></tr>
                        <tr><th>Bid Increment</th><td>₱<?php echo number_format($auction['bid_increment'], 2); ?></td></tr>
                        <tr><th>End Time</th><td><?php echo date('F j, Y g:i A', strtotime($auction['end_time'])); ?></td></tr>
                        <?php if (!empty($auction['location'])): ?>
                        <tr><th>Item Location</th><td><?php echo htmlspecialchars($auction['location']); ?></td></tr>
                        <?php endif; ?>
                    </table>
                </div>
                
                <!-- New Shipping & Contact Tab -->
                <div id="shipping" class="tab-content">
                    <table class="details-table">
                        <tr>
                            <th><i class="fas fa-truck"></i> Shipping Option</th>
                            <td>
                                <?php 
                                $shipping_options = [
                                    'seller' => 'Seller pays shipping',
                                    'buyer' => 'Buyer pays shipping',
                                    'free' => 'Free shipping',
                                    'local' => 'Local pickup only'
                                ];
                                $shipping = $auction['shipping'] ?? 'buyer';
                                echo isset($shipping_options[$shipping]) ? $shipping_options[$shipping] : ucfirst($shipping);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-phone"></i> Seller Contact</th>
                            <td>
                                <?php if (!empty($auction['seller_phone'])): ?>
                                    <a href="tel:<?php echo htmlspecialchars($auction['seller_phone']); ?>" class="contact-link">
                                        <i class="fas fa-phone-alt"></i> <?php echo htmlspecialchars($auction['seller_phone']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Contact seller after winning</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-envelope"></i> Seller Email</th>
                            <td>
                                <?php if (!empty($auction['seller_email'])): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($auction['seller_email']); ?>" class="contact-link">
                                        <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($auction['seller_email']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Contact via platform</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if (!empty($auction['location'])): ?>
                        <tr>
                            <th><i class="fas fa-map-marker-alt"></i> Location</th>
                            <td><?php echo htmlspecialchars($auction['location']); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                    
                    <?php if ($_SESSION['user_id'] != $auction['seller_id'] && $auction['status'] == 'active'): ?>
                    
                    <?php endif; ?>
                </div>
                
                <div id="bids" class="tab-content">
                    <div class="bid-history">
                        <?php if (empty($bid_history)): ?>
                            <p>No bids yet. Be the first to bid!</p>
                        <?php else: ?>
                            <?php foreach ($bid_history as $bid): ?>
                                <div class="bid-row">
                                    <span class="bidder"><?php echo htmlspecialchars($bid['username']); ?></span>
                                    <span class="amount">₱<?php echo number_format($bid['amount'], 2); ?></span>
                                    <span class="time"><?php echo date('M d, H:i', strtotime($bid['bid_time'])); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const auctionId = <?php echo $auction['id']; ?>;
const currentPrice = <?php echo $auction['current_price']; ?>;
const bidIncrement = <?php echo $auction['bid_increment']; ?>;

function placeBid() {
    const amount = document.getElementById('bid_amount').value;
    
    if (!amount || amount <= 0) {
        alert('Please enter a valid bid amount');
        return;
    }
    
    const minBid = currentPrice + bidIncrement;
    if (parseFloat(amount) < minBid) {
        alert('Bid must be at least ₱' + minBid.toFixed(2));
        return;
    }
    
    // Show loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Placing...';
    btn.disabled = true;
    
    // Create form data
    const formData = new URLSearchParams();
    formData.append('auction_id', auctionId);
    formData.append('amount', amount);
    
    fetch('index.php?action=place-bid', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✓ ' + data.message);
            location.reload();
        } else {
            alert('✗ ' + data.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function startCountdown() {
    const timerDiv = document.querySelector('.countdown-large');
    if (!timerDiv) return;
    
    const endTimeStr = timerDiv.dataset.endtime;
    if (!endTimeStr) return;
    
    const endTime = new Date(endTimeStr).getTime();
    const timerSpan = timerDiv.querySelector('.timer');
    
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = endTime - now;
        
        if (distance < 0) {
            timerSpan.innerHTML = "Auction Ended";
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        let display = '';
        if (days > 0) display += days + 'd ';
        display += hours.toString().padStart(2, '0') + 'h ';
        display += minutes.toString().padStart(2, '0') + 'm ';
        display += seconds.toString().padStart(2, '0') + 's';
        timerSpan.innerHTML = display;
    }
    
    updateCountdown();
    setInterval(updateCountdown, 1000);
}

function showTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.getElementById(tabId).classList.add('active');
    
    document.querySelectorAll('.tabs .tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
}

function showContactInfo() {
    alert('Contact information available after winning the auction or when seller provides contact details.');
}

startCountdown();
</script>

<style>
.alert-info {
    background: rgba(23, 162, 184, 0.15);
    border: 1px solid rgba(23, 162, 184, 0.3);
    color: #17a2b8;
    padding: 1rem;
    border-radius: 8px;
    text-align: center;
}

.alert-error {
    background: rgba(220, 53, 69, 0.15);
    border: 1px solid rgba(220, 53, 69, 0.3);
    color: #dc3545;
    padding: 1rem;
    border-radius: 8px;
    text-align: center;
}

.countdown-large .timer {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--primary-gold);
}

.bid-row {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-bottom: 1px solid var(--border-color);
}

.bid-row .amount {
    color: var(--primary-gold);
    font-weight: bold;
}

.bid-row .time {
    color: var(--text-muted);
    font-size: 12px;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

.tabs .tab-btn {
    background: none;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    color: var(--text-secondary);
    transition: all 0.3s;
}

.tabs .tab-btn.active {
    color: var(--primary-gold);
    border-bottom: 2px solid var(--primary-gold);
}

.details-table {
    width: 100%;
}

.details-table tr {
    border-bottom: 1px solid var(--border-color);
}

.details-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: var(--primary-gold);
    width: 40%;
    background: rgba(255, 215, 0, 0.05);
}

.details-table td {
    padding: 12px;
    color: var(--text-secondary);
}

.contact-link {
    color: var(--primary-gold);
    text-decoration: none;
    transition: all 0.3s;
}

.contact-link:hover {
    text-decoration: underline;
}

.contact-seller-box {
    margin-top: 1.5rem;
    padding: 1.5rem;
    background: var(--dark-card);
    border-radius: 16px;
    text-align: center;
    border: 1px solid var(--border-color);
}

.contact-seller-box h4 {
    color: var(--primary-gold);
    margin-bottom: 0.5rem;
}

.contact-seller-box p {
    color: var(--text-muted);
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.text-muted {
    color: var(--text-muted);
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@media (max-width: 768px) {
    .details-table th,
    .details-table td {
        padding: 8px;
        font-size: 13px;
    }
    
    .tabs .tab-btn {
        padding: 8px 12px;
        font-size: 12px;
    }

    .seller-link {
        color: var(--primary-gold);
        text-decoration: none;
        transition: all 0.3s;
    }

    .seller-link:hover {
        text-decoration: underline;
        color: var(--primary-gold-light);
    }
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>