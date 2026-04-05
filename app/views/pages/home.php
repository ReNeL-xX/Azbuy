<?php 
$page_title = "Home";
ob_start();
?>

<div class="hero-section">
    <div class="hero-content">
        <h1>Welcome to <span class="gold">Azbuy</span></h1>
        <p>The premier digital marketplace where collectors and enthusiasts compete for extraordinary items</p>
        <div class="hero-buttons">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="index.php?action=register" class="btn btn-primary">Get Started</a>
                <a href="index.php?action=login" class="btn btn-secondary">Login</a>
            <?php else: ?>
                <a href="index.php?action=dashboard" class="btn btn-primary">Start Bidding</a>
                <a href="index.php?action=create-auction" class="btn btn-secondary">Sell Items</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="features-section">
    <h2>Why Choose Azbuy?</h2>
    <div class="features-grid">
        <div class="feature-card">
            <i class="fas fa-gavel"></i>
            <h3>Live Bidding</h3>
            <p>Real-time auction experience with instant bid updates</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-shield-alt"></i>
            <h3>Secure Transactions</h3>
            <p>Safe and verified payment processing system</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-clock"></i>
            <h3>Time-Limited Auctions</h3>
            <p>Exciting countdowns for every auction item</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-chart-line"></i>
            <h3>Track Activities</h3>
            <p>Monitor your bids and auction history</p>
        </div>
    </div>
</div>

<div class="featured-auctions">
    <h2>🔥 Featured Auctions</h2>
    <div class="auctions-grid">
           <div class="auction-card">
            <div class="auction-image">
                <img src="http://localhost/AzBuy/public/assets/img/rolex.jpg" alt="Vintage Rolex Watch">
</xai:function_call. 

<xai:function_call name="edit_file">
<parameter name="path">c:/xampp/htdocs/AzBuy/app/views/pages/home.php
                <div class="auction-timer">
                    <i class="fas fa-hourglass-half"></i> <span class="countdown" data-time="86400">24h 00m 00s</span>
                </div>
                <div class="bid-count-badge">15 bids</div>
            </div>
            <div class="auction-info">
                <h3>Vintage Rolex Watch 1989</h3>
                <p class="category"><i class="fas fa-tag"></i> Collectibles</p>
                <div class="price-info">
                    <span class="current-price">$1,250.00</span>
                    <span class="starting-price">Started: $500</span>
                </div>
                <a href="index.php?action=view-auction&id=1" class="btn btn-primary btn-block">Place Bid</a>
            </div>
        </div>

        <div class="auction-card">
            <div class="auction-image">
                <img src="http://localhost/AzBuy/public/assets/img/MacBookProM3Max.jpg" alt="MacBook Pro M3 Max" onerror="this.src='https://via.placeholder.com/300x200/1a1a1a/ffd700?text=No+Image'">
                <div class="auction-timer">
                    <i class="fas fa-hourglass-half"></i> <span class="countdown" data-time="172800">48h 00m 00s</span>
                </div>
                <div class="bid-count-badge">32 bids</div>
            </div>
            <div class="auction-info">
                <h3>MacBook Pro M3 Max</h3>
                <p class="category"><i class="fas fa-tag"></i> Electronics</p>
                <div class="price-info">
                    <span class="current-price">$2,450.00</span>
                    <span class="starting-price">Started: $1,800</span>
                </div>
                <a href="index.php?action=view-auction&id=2" class="btn btn-primary btn-block">Place Bid</a>
            </div>
        </div>

        <div class="auction-card">
            <div class="auction-image">
                <img src="http://localhost/AzBuy/public/assets/img/OriginalAbstractPainting.jpg" alt="Original Abstract Painting" onerror="this.src='https://via.placeholder.com/300x200/1a1a1a/ffd700?text=No+Image'">
                <div class="auction-timer">
                    <i class="fas fa-hourglass-half"></i> <span class="countdown" data-time="3600">1h 00m 00s</span>
                </div>
                <div class="bid-count-badge">8 bids</div>
            </div>
            <div class="auction-info">
                <h3>Original Abstract Painting</h3>
                <p class="category"><i class="fas fa-tag"></i> Art</p>
                <div class="price-info">
                    <span class="current-price">$3,200.00</span>
                    <span class="starting-price">Started: $2,500</span>
                </div>
                <a href="index.php?action=view-auction&id=3" class="btn btn-primary btn-block">Place Bid</a>
            </div>
        </div>

        <div class="auction-card">
            <div class="auction-image">
                <img src="http://localhost/AzBuy/public/assets/img/NikeAirJordan1Limited.jpg" alt="Nike Air Jordan 1 Limited" onerror="this.src='https://via.placeholder.com/300x200/1a1a1a/ffd700?text=No+Image'">
                <div class="auction-timer">
                    <i class="fas fa-hourglass-half"></i> <span class="countdown" data-time="259200">72h 00m 00s</span>
                </div>
                <div class="bid-count-badge">23 bids</div>
            </div>
            <div class="auction-info">
                <h3>Nike Air Jordan 1 Limited</h3>
                <p class="category"><i class="fas fa-tag"></i> Fashion</p>
                <div class="price-info">
                    <span class="current-price">$890.00</span>
                    <span class="starting-price">Started: $400</span>
                </div>
                <a href="index.php?action=view-auction&id=4" class="btn btn-primary btn-block">Place Bid</a>
            </div>
        </div>
    </div>
</div>

<div class="stats-section">
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <h3>10,000+</h3>
            <p>Active Bidders</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-gavel"></i>
            <h3>5,000+</h3>
            <p>Auctions Completed</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-dollar-sign"></i>
            <h3>$2.5M+</h3>
            <p>Total Sales</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-clock"></i>
            <h3>24/7</h3>
            <p>Live Support</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>