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
                            <span class="current-price">$<?php echo number_format($auction['current_price'], 2); ?></span>
                            <span class="starting-price">Started: $<?php echo number_format($auction['starting_price'], 2); ?></span>
                        </div>
                        <a href="index.php?action=view-auction&id=<?php echo $auction['id']; ?>" class="btn btn-primary btn-block">View Auction</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Countdown timers
function initCountdowns() {
    const countdowns = document.querySelectorAll('.countdown');
    countdowns.forEach(el => {
        const endTime = new Date(el.dataset.endtime).getTime();
        
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
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            let display = '';
            if (days > 0) display += days + 'd ';
            display += hours + 'h ' + minutes + 'm';
            el.innerHTML = display;
        }
        
        update();
        setInterval(update, 1000);
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