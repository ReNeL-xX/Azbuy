<?php 
$page_title = "My Bidding Activity";

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
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
                <span class="value">47</span>
            </div>
        </div>
        <div class="summary-card">
            <i class="fas fa-trophy"></i>
            <div>
                <span class="label">Auctions Won</span>
                <span class="value">3</span>
            </div>
        </div>
        <div class="summary-card">
            <i class="fas fa-dollar-sign"></i>
            <div>
                <span class="label">Total Spent</span>
                <span class="value">$2,340.00</span>
            </div>
        </div>
        <div class="summary-card">
            <i class="fas fa-clock"></i>
            <div>
                <span class="label">Active Bids</span>
                <span class="value">5</span>
            </div>
        </div>
    </div>
    
    <div class="items-tabs">
        <button class="tab-btn active" onclick="filterBids('active')">Active Bids (5)</button>
        <button class="tab-btn" onclick="filterBids('winning')">Winning (2)</button>
        <button class="tab-btn" onclick="filterBids('lost')">Lost (8)</button>
        <button class="tab-btn" onclick="filterBids('won')">Won - Pending Payment (1)</button>
    </div>
    
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
                <tr class="winning-row">
                    <td class="item-cell">
                        <div class="item-info">
                             <img src="http://localhost/AzBuy/public/assets/img/rolex.jpg" alt="Vintage Rolex Watch" onerror="this.src='https://via.placeholder.com/300x200/1a1a1a/ffd700?text=No+Image'">
                            <div>
                                <strong>Vintage Rolex Watch</strong>
                                <small>Seller: luxury_collector</small>
                            </div>
                        </div>
                    </td>
                    <td class="price">$1,250.00</td>
                    <td class="price">$1,250.00</td>
                    <td><span class="status-badge winning">You're Winning</span></td>
                    <td><span class="countdown-small" data-time="86400">24h 00m</span></td>
                    <td><a href="index.php?action=view-auction&id=1" class="btn-sm btn-primary">Increase Bid</a></td>
                </tr>
                <tr class="active-row">
                    <td class="item-cell">
                        <div class="item-info">
                            <img src="http://localhost/AzBuy/public/assets/img/MacBookProM3Max.jpg" alt="MacBook Pro M3 Max" onerror="this.src='https://via.placeholder.com/300x200/1a1a1a/ffd700?text=No+Image'">
                            <div>
                                <strong>MacBook Pro M3</strong>
                                <small>Seller: tech_haven</small>
                            </div>
                        </div>
                    </td>
                    <td class="price">$2,300.00</td>
                    <td class="price">$2,450.00</td>
                    <td><span class="status-badge outbid">Outbid</span></td>
                    <td><span class="countdown-small" data-time="172800">48h 00m</span></td>
                    <td><a href="index.php?action=view-auction&id=2" class="btn-sm btn-primary">Place Bid</a></td>
                </tr>
                <tr class="payment-row">
                    <td class="item-cell">
                        <div class="item-info">
                            <img src="http://localhost/AzBuy/public/assets/img/NikeAirJordan1Limited.jpg" alt="Nike Air Jordan 1" onerror="this.src='https://via.placeholder.com/300x200/1a1a1a/ffd700?text=No+Image'">
                            <div>
                                <strong>Nike Air Jordan 1</strong>
                                <small>Seller: sneakerhead</small>
                            </div>
                        </div>
                    </td>
                    <td class="price">$890.00</td>
                    <td class="price">$890.00</td>
                    <td><span class="status-badge won">Won - Payment Required</span></td>
                    <td>Payment due in 23h 45m</td>
                    <td><button class="btn-sm btn-success" onclick="openPaymentModal()">Pay Now</button></td>
                </tr>
                <tr class="lost-row">
                    <td class="item-cell">
                        <div class="item-info">
                             <img src="http://localhost/AzBuy/public/assets/img/OriginalAbstractPainting.jpg" alt="Original Abstract Painting" onerror="this.src='https://via.placeholder.com/300x200/1a1a1a/ffd700?text=No+Image'">
                            <div>
                                <strong>Abstract Painting</strong>
                                <small>Seller: art_gallery</small>
                            </div>
                        </div>
                    </td>
                    <td class="price">$2,800.00</td>
                    <td class="price">$3,200.00</td>
                    <td><span class="status-badge lost">Lost</span></td>
                    <td>Auction Ended</td>
                    <td><a href="#" class="btn-sm btn-secondary">View Item</a></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="payment-modal" id="paymentModal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Complete Payment</h3>
                <button class="close-modal" onclick="closePaymentModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>You won: <strong>Nike Air Jordan 1 Limited</strong></p>
                <p>Winning bid: <strong class="price">$890.00</strong></p>
                <div class="payment-methods">
                    <label><input type="radio" name="payment" value="balance"> Use Wallet Balance ($1,234.56 available)</label>
                    <label><input type="radio" name="payment" value="card"> Credit/Debit Card</label>
                    <label><input type="radio" name="payment" value="paypal"> PayPal</label>
                </div>
                <div class="card-details" style="display: none;">
                    <input type="text" placeholder="Card Number">
                    <div class="form-row">
                        <input type="text" placeholder="MM/YY">
                        <input type="text" placeholder="CVC">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closePaymentModal()">Cancel</button>
                <button class="btn btn-primary" onclick="processPayment()">Confirm Payment</button>
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