<?php 
$page_title = "View Auction";
ob_start();
?>

<div class="auction-detail">
    <div class="auction-detail-grid">
        <div class="auction-image-section">
            <div class="main-image">
                <img src="http://localhost/AzBuy/public/assets/img/rolex.jpg" alt="Vintage Rolex Watch" onerror="this.src='https://via.placeholder.com/300x200/1a1a1a/ffd700?text=No+Image'">
            </div>
         
        </div>
        
        <div class="auction-info-section">
            <div class="auction-title">
                <h1>Vintage Rolex Watch 1989 - Excellent Condition</h1>
                <div class="seller-info">
                    <i class="fas fa-store"></i> Sold by <strong>luxury_collector</strong>
                    <span class="rating"><i class="fas fa-star"></i> 4.9 (234 reviews)</span>
                </div>
            </div>
            
            <div class="bid-status-card">
                <div class="current-bid-section">
                    <span class="label">Current Bid</span>
                    <div class="current-bid-amount">$1,250.00</div>
                    <span class="bid-count">15 bids from 8 bidders</span>
                </div>
                
                <div class="time-left-section">
                    <span class="label"><i class="fas fa-hourglass-half"></i> Time Left</span>
                    <div class="countdown-large" data-time="86400">
                        <span class="timer">24h 00m 00s</span>
                    </div>
                </div>
                
                <div class="bid-form-section">
                    <label>Your Bid (USD)</label>
                    <div class="bid-input-group">
                        <span class="currency">$</span>
                        <input type="number" id="bid_amount" step="1.00" value="1251.00" placeholder="Enter amount">
                        <button onclick="placeBid()" class="btn btn-primary">Place Bid</button>
                    </div>
                    <div class="bid-info">
                        <p><i class="fas fa-info-circle"></i> Minimum bid: $1,251.00</p>
                        <p><i class="fas fa-chart-line"></i> Bid increment: $1.00</p>
                    </div>
                </div>
            </div>
            
            <div class="auction-details-tabs">
                <div class="tabs">
                    <button class="tab-btn active" onclick="showTab('description')">Description</button>
                    <button class="tab-btn" onclick="showTab('details')">Item Details</button>
                    <button class="tab-btn" onclick="showTab('shipping')">Shipping & Payment</button>
                    <button class="tab-btn" onclick="showTab('bids')">Bid History</button>
                </div>
                
                <div id="description" class="tab-content active">
                    <p>This stunning vintage Rolex watch from 1989 is in excellent condition. Features include:</p>
                    <ul>
                        <li>Automatic movement</li>
                        <li>Sapphire crystal glass</li>
                        <li>Original leather band</li>
                        <li>Comes with original box and papers</li>
                        <li>Recently serviced and polished</li>
                    </ul>
                    <p>The watch keeps excellent time and shows minimal signs of wear. A perfect addition to any collector's portfolio or a timeless gift for a special occasion.</p>
                </div>
                
                <div id="details" class="tab-content">
                    <table class="details-table">
                        <tr><th>Brand</th><td>Rolex</td></tr>
                        <tr><th>Model</th><td>Datejust</td></tr>
                        <tr><th>Year</th><td>1989</td></tr>
                        <tr><th>Condition</th><td>Excellent</td></tr>
                        <tr><th>Box & Papers</th><td>Yes</td></tr>
                        <tr><th>Warranty</th><td>30 days</td></tr>
                    </table>
                </div>
                
                <div id="shipping" class="tab-content">
                    <h4>Shipping Information</h4>
                    <p><i class="fas fa-truck"></i> Free shipping within US</p>
                    <p><i class="fas fa-globe"></i> International shipping available</p>
                    <p><i class="fas fa-clock"></i> Ships within 2 business days</p>
                    <h4>Payment Methods</h4>
                    <p><i class="fab fa-cc-visa"></i> <i class="fab fa-cc-mastercard"></i> <i class="fab fa-cc-amex"></i> <i class="fab fa-paypal"></i> <i class="fas fa-university"></i> Bank Transfer</p>
                </div>
                
                <div id="bids" class="tab-content">
                    <div class="bid-history">
                        <div class="bid-row">
                            <span class="bidder">s****r</span>
                            <span class="amount">$1,250.00</span>
                            <span class="time">2 minutes ago</span>
                        </div>
                        <div class="bid-row">
                            <span class="bidder">c****r</span>
                            <span class="amount">$1,200.00</span>
                            <span class="time">15 minutes ago</span>
                        </div>
                        <div class="bid-row">
                            <span class="bidder">a****e</span>
                            <span class="amount">$1,150.00</span>
                            <span class="time">1 hour ago</span>
                        </div>
                        <div class="bid-row">
                            <span class="bidder">m****n</span>
                            <span class="amount">$1,000.00</span>
                            <span class="time">3 hours ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>