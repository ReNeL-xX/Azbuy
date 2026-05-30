<?php 
$page_title = "Home";

// Hardcode base URL for Hostinger
$base_url = 'https://azbuy.bsit2a.com/public';

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
    <div class="features-grid horizontal">
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
                        <?php if (!empty($auction['image_url'])): ?>
    <?php 
    $clean_url = ltrim($auction['image_url'], '/');
    $image_src = 'https://azbuy.bsit2a.com/' . $clean_url;
    ?>
    <img src="<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>" onerror="this.src='https://placehold.co/400x300/1a1a1a/gold?text=No+Image'">
<?php else: ?>
    <img src="https://placehold.co/400x300/1a1a1a/gold?text=No+Image" alt="<?php echo htmlspecialchars($auction['title']); ?>">
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

<style>
.hero-section {
    position: relative;
    min-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at 30% 50%, rgba(255, 215, 0, 0.05) 0%, var(--dark-bg) 100%);
    overflow: hidden;
}

.hero-content {
    text-align: center;
    z-index: 1;
    padding: 2rem;
}

.hero-content h1 {
    font-size: 4.5rem;
    margin-bottom: 1rem;
    animation: fadeInUp 0.8s ease;
}

.hero-content .gold {
    background: var(--gradient-gold);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    display: inline-block;
}

.hero-content p {
    font-size: 1.2rem;
    max-width: 600px;
    margin: 0 auto 2rem;
    color: var(--text-secondary);
    animation: fadeInUp 0.8s ease 0.2s backwards;
}

.hero-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    animation: fadeInUp 0.8s ease 0.4s backwards;
}

.features-section {
    padding: 5rem 2rem;
    background: var(--dark-card);
    position: relative;
}

.features-section h2 {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 3rem;
    position: relative;
    display: inline-block;
    width: 100%;
}

.features-section h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: var(--gradient-gold);
    border-radius: 3px;
}

.features-grid.horizontal {
    display: flex;
    flex-direction: row;
    justify-content: center;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    flex-wrap: wrap;
}

.feature-card {
    flex: 1;
    min-width: 200px;
    text-align: center;
    padding: 2rem;
    background: var(--dark-elevated);
    border-radius: 20px;
    transition: var(--transition);
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 215, 0, 0.1), transparent);
    transition: left 0.6s;
}

.feature-card:hover::before {
    left: 100%;
}

.feature-card:hover {
    transform: translateY(-10px);
    border-color: var(--primary-gold);
    box-shadow: var(--shadow-gold);
}

.feature-card i {
    font-size: 3rem;
    background: var(--gradient-gold);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    margin-bottom: 1rem;
}

.feature-card h3 {
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.feature-card p {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.featured-auctions {
    padding: 4rem 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.featured-auctions h2 {
    font-size: 2rem;
    margin-bottom: 2rem;
    position: relative;
    display: inline-block;
}

.featured-auctions h2::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 50px;
    height: 3px;
    background: var(--gradient-gold);
}

.auctions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
}

.auction-card {
    background: var(--dark-elevated);
    border-radius: 16px;
    overflow: hidden;
    transition: var(--transition);
    border: 1px solid var(--border-color);
    position: relative;
}

.auction-card:hover {
    transform: translateY(-8px);
    border-color: var(--primary-gold);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

.auction-image {
    position: relative;
    height: 240px;
    background: var(--dark-card);
    overflow: hidden;
}

.auction-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.auction-card:hover .auction-image img {
    transform: scale(1.05);
}

.auction-timer {
    position: absolute;
    bottom: 12px;
    right: 12px;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(5px);
    padding: 6px 12px;
    border-radius: 50px;
    font-size: 12px;
    color: var(--primary-gold);
    font-weight: 600;
    border: 1px solid var(--border-color);
}

.bid-count-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: var(--gradient-gold);
    color: #000;
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 700;
}

.auction-info {
    padding: 1.2rem;
}

.auction-info h3 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.seller {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
}

.category {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-bottom: 0.8rem;
}

.price-info {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin: 0.8rem 0;
}

.current-price {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--primary-gold);
}

.starting-price {
    font-size: 0.75rem;
    color: var(--text-muted);
}

.stats-section {
    background: linear-gradient(135deg, var(--dark-card) 0%, var(--dark-bg) 100%);
    padding: 4rem 2rem;
    border-top: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
}

.stats-grid {
    display: flex;
    justify-content: center;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
    flex-wrap: wrap;
}

.stat-card {
    flex: 1;
    min-width: 200px;
    padding: 1.5rem;
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card i {
    font-size: 2.5rem;
    background: var(--gradient-gold);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    margin-bottom: 0.5rem;
}

.stat-card h3 {
    font-size: 2rem;
    color: var(--primary-gold);
}

.stat-card p {
    color: var(--text-muted);
}

.btn-primary, .btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 12px 28px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
    border: none;
    cursor: pointer;
    font-size: 14px;
    letter-spacing: 0.5px;
}

.btn-primary {
    background: var(--gradient-gold);
    color: #000000;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
}

.btn-secondary {
    background: transparent;
    color: var(--primary-gold);
    border: 2px solid var(--primary-gold);
}

.btn-secondary:hover {
    background: var(--primary-gold);
    color: #000000;
    transform: translateY(-2px);
}

.btn-block {
    display: block;
    width: 100%;
    text-align: center;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 1024px) {
    .features-grid.horizontal {
        gap: 1.5rem;
    }
    
    .feature-card {
        min-width: 180px;
    }
}

@media (max-width: 768px) {
    .hero-content h1 {
        font-size: 2.5rem;
    }
    
    .features-grid.horizontal {
        flex-direction: column;
        gap: 1rem;
    }
    
    .feature-card {
        width: 100%;
    }
    
    .auctions-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        flex-direction: column;
        gap: 1rem;
    }
    
    .stats-grid .stat-card {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .hero-content h1 {
        font-size: 1.8rem;
    }
    
    .hero-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .hero-buttons .btn {
        width: 100%;
        max-width: 200px;
    }
}
</style>

<script>
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