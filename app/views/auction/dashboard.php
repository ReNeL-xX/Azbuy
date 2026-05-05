<?php 
$page_title = "Marketplace";

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

ob_start();
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h1><i class="fas fa-store"></i> Active Auctions</h1>
            <p>Discover and bid on amazing items</p>
        </div>
        <a href="index.php?action=create-auction" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Auction
        </a>
    </div>
    
    <?php if (empty($active_auctions)): ?>
        <div class="empty-state">
            <i class="fas fa-gavel"></i>
            <h3>No Active Auctions</h3>
            <p>Be the first to create an auction!</p>
            <a href="index.php?action=create-auction" class="btn btn-primary">Create Auction</a>
        </div>
    <?php else: ?>
        <div class="auctions-grid">
            <?php foreach ($active_auctions as $auction): ?>
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
    <?php endif; ?>
</div>

<style>
.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.dashboard-header h1 {
    color: var(--primary-gold);
    margin-bottom: 0.5rem;
}

.dashboard-header p {
    color: var(--text-secondary);
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
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
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

.btn-block {
    display: block;
    width: 100%;
    text-align: center;
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }
    
    .dashboard-header {
        flex-direction: column;
        text-align: center;
    }
    
    .auctions-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .auction-image {
        height: 200px;
    }
    
    .current-price {
        font-size: 1.2rem;
    }
}
</style>

<script>
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

initCountdowns();
</script>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>