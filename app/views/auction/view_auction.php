<?php 
$page_title = $auction['title'];

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

// Hardcode base URL for Hostinger
$base_url = 'https://azbuy.bsit2a.com/public';

ob_start();
?>

<div class="auction-detail">
    <div class="auction-detail-grid">
        <div class="auction-image-section">
            <div class="main-image">
                <?php if (!empty($auction['image_url'])): ?>
    <?php 
    $clean_url = ltrim($auction['image_url'], '/');
    $image_src = 'https://azbuy.bsit2a.com/' . $clean_url;
    ?>
    <img src="<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>" onerror="this.src='https://placehold.co/600x400/1a1a1a/gold?text=No+Image'">
<?php else: ?>
    <img src="https://placehold.co/600x400/1a1a1a/gold?text=No+Image" alt="<?php echo htmlspecialchars($auction['title']); ?>">
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
    
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Placing...';
    btn.disabled = true;
    
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

startCountdown();
</script>

<style>
.auction-detail {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.auction-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.auction-image-section {
    background: var(--dark-elevated);
    border-radius: 24px;
    padding: 1rem;
    border: 1px solid var(--border-color);
}

.main-image {
    border-radius: 16px;
    overflow: hidden;
    background: var(--dark-card);
}

.main-image img {
    width: 100%;
    height: auto;
    object-fit: contain;
    display: block;
}

.auction-info-section {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.auction-title h1 {
    font-size: 1.8rem;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.seller-info {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.seller-link {
    color: var(--primary-gold);
    text-decoration: none;
    transition: all 0.3s;
}

.seller-link:hover {
    text-decoration: underline;
}

.bid-status-card {
    background: var(--dark-elevated);
    border-radius: 20px;
    padding: 1.5rem;
    border: 1px solid var(--border-color);
}

.current-bid-section {
    text-align: center;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
    margin-bottom: 1rem;
}

.current-bid-section .label {
    font-size: 0.85rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.current-bid-amount {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--primary-gold);
    margin: 0.5rem 0;
}

.bid-count {
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.time-left-section {
    text-align: center;
    padding: 1rem;
    background: var(--dark-card);
    border-radius: 12px;
    margin-bottom: 1rem;
}

.time-left-section .label {
    font-size: 0.8rem;
    color: var(--text-muted);
    display: block;
    margin-bottom: 0.5rem;
}

.countdown-large .timer {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--primary-gold);
}

.bid-form-section {
    margin-top: 1rem;
}

.bid-form-section label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text-primary);
}

.bid-input-group {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.currency {
    background: var(--dark-card);
    padding: 12px 16px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    font-weight: bold;
    color: var(--primary-gold);
}

.bid-input-group input {
    flex: 1;
    padding: 12px 16px;
    background: var(--dark-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    color: var(--text-primary);
    font-size: 1rem;
}

.bid-input-group input:focus {
    outline: none;
    border-color: var(--primary-gold);
}

.bid-info {
    margin-top: 0.75rem;
    font-size: 0.8rem;
    color: var(--text-muted);
}

.bid-info p {
    margin: 0.25rem 0;
}

.bid-info i {
    color: var(--primary-gold);
    margin-right: 6px;
}

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

.auction-details-tabs {
    background: var(--dark-elevated);
    border-radius: 20px;
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.tabs {
    display: flex;
    gap: 0;
    border-bottom: 1px solid var(--border-color);
    background: var(--dark-card);
    overflow-x: auto;
}

.tabs .tab-btn {
    background: none;
    border: none;
    padding: 12px 24px;
    cursor: pointer;
    color: var(--text-secondary);
    transition: all 0.3s;
    font-size: 0.85rem;
    font-weight: 500;
    white-space: nowrap;
}

.tabs .tab-btn:hover {
    color: var(--primary-gold);
    background: rgba(255, 215, 0, 0.05);
}

.tabs .tab-btn.active {
    color: var(--primary-gold);
    border-bottom: 2px solid var(--primary-gold);
}

.tab-content {
    display: none;
    padding: 1.5rem;
}

.tab-content.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.details-table {
    width: 100%;
    border-collapse: collapse;
}

.details-table tr {
    border-bottom: 1px solid var(--border-color);
}

.details-table tr:last-child {
    border-bottom: none;
}

.details-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: var(--primary-gold);
    width: 40%;
    background: rgba(255, 215, 0, 0.03);
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

.text-muted {
    color: var(--text-muted);
}

.bid-history {
    max-height: 400px;
    overflow-y: auto;
}

.bid-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    border-bottom: 1px solid var(--border-color);
}

.bid-row:last-child {
    border-bottom: none;
}

.bid-row .bidder {
    font-weight: 500;
    color: var(--text-primary);
}

.bid-row .amount {
    color: var(--primary-gold);
    font-weight: bold;
    font-size: 1rem;
}

.bid-row .time {
    color: var(--text-muted);
    font-size: 0.75rem;
}

.btn-primary {
    background: linear-gradient(135deg, #FFD700 0%, #DAA520 100%);
    color: #000;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    border: none;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

@media (max-width: 1024px) {
    .auction-detail-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .auction-detail {
        padding: 1rem;
    }
    
    .current-bid-amount {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    .auction-title h1 {
        font-size: 1.3rem;
    }
    
    .tabs .tab-btn {
        padding: 10px 16px;
        font-size: 0.75rem;
    }
    
    .tab-content {
        padding: 1rem;
    }
    
    .details-table th,
    .details-table td {
        padding: 8px;
        font-size: 0.8rem;
    }
    
    .bid-input-group {
        flex-direction: column;
    }
    
    .currency {
        width: 100%;
        text-align: center;
    }
    
    .bid-input-group input {
        width: 100%;
    }
    
    .bid-input-group button {
        width: 100%;
        justify-content: center;
    }
    
    .countdown-large .timer {
        font-size: 1.2rem;
    }
}

@media (max-width: 480px) {
    .current-bid-amount {
        font-size: 1.5rem;
    }
    
    .tabs {
        flex-wrap: wrap;
    }
    
    .tabs .tab-btn {
        flex: 1;
        text-align: center;
    }
    
    .details-table th,
    .details-table td {
        display: block;
        width: 100%;
    }
    
    .details-table th {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .details-table td {
        padding-top: 0;
    }
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>