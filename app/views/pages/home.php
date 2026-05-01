<?php 
$page_title = "Home";

// Get active auctions from database
require_once dirname(__DIR__) . '/../models/Auction.php';
require_once dirname(__DIR__) . '/../../config/Database.php';

$database = new Database();
$conn = $database->connect();
$auctionModel = new Auction($conn);

// Get active auctions (limit to 4 for featured section)
$featured_auctions = $auctionModel->getAllActive(4, 0);

$conn->close();

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
    
    <?php if (empty($featured_auctions)): ?>
        <div class="empty-state" style="text-align: center; padding: 3rem;">
            <i class="fas fa-gavel" style="font-size: 4rem; color: var(--primary-gold); margin-bottom: 1rem;"></i>
            <h3>No Active Auctions</h3>
            <p>Be the first to create an auction!</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?action=create-auction" class="btn btn-primary">Create Auction</a>
            <?php else: ?>
                <a href="index.php?action=register" class="btn btn-primary">Get Started</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="auctions-grid">
            <?php foreach ($featured_auctions as $auction): ?>
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
                        <div class="bid-count-badge"><?php echo $auction['bid_count']; ?> bids</div>
                    </div>
                    <div class="auction-info">
                        <h3><?php echo htmlspecialchars($auction['title']); ?></h3>
                        <p class="seller">by <?php echo htmlspecialchars($auction['seller_name']); ?></p>
                        <p class="category"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($auction['category']); ?></p>
<div class="price-info">
                            <span class="current-price">₱<?php echo number_format($auction['current_price'], 2); ?></span>
                            <span class="starting-price">Started: ₱<?php echo number_format($auction['starting_price'], 2); ?></span>
                        </div>
                        <a href="index.php?action=view-auction&id=<?php echo $auction['id']; ?>" class="btn btn-primary btn-block">View Auction</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($featured_auctions) >= 4): ?>
            <div style="text-align: center; margin-top: 2rem;">
                <a href="index.php?action=dashboard" class="btn btn-secondary">View All Auctions →</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
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
            <i class="fas fa-peso-sign"></i>
            <h3>₱2.5M+</h3>
            <p>Total Sales</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-clock"></i>
            <h3>24/7</h3>
            <p>Live Support</p>
        </div>
    </div>
</div>

<script>
// Countdown timers for featured auctions
function initHomeCountdowns() {
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

initHomeCountdowns();
</script>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>