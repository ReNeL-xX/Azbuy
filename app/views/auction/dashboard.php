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
    
    <!-- Search and Filter Bar -->
    <div class="filter-bar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search auctions by title...">
        </div>
        
        <div class="filter-group">
            <select id="categoryFilter">
                <option value="all">All Categories</option>
                <option value="Electronics">Electronics</option>
                <option value="Collectibles">Collectibles</option>
                <option value="Art">Art</option>
                <option value="Furniture">Furniture</option>
                <option value="Vehicles">Vehicles</option>
                <option value="Fashion">Fashion</option>
                <option value="Jewelry">Jewelry</option>
                <option value="Sports">Sports Memorabilia</option>
                <option value="Books">Books & Magazines</option>
                <option value="Music">Music & Instruments</option>
                <option value="Toys">Toys & Hobbies</option>
            </select>
        </div>
        
        <div class="filter-group">
            <select id="sortFilter">
                <option value="time_asc">Time Left (Lowest First)</option>
                <option value="price_asc">Price (Low to High)</option>
                <option value="price_desc">Price (High to Low)</option>
                <option value="bids_desc">Most Bids</option>
                <option value="newest">Newest First</option>
            </select>
        </div>
        
        <div class="filter-results">
            <span id="resultsCount">0</span> auctions found
        </div>
    </div>
    
    <?php if (empty($active_auctions)): ?>
        <div class="empty-state">
            <i class="fas fa-gavel"></i>
            <h3>No Active Auctions</h3>
            <p>Be the first to create an auction!</p>
            <a href="index.php?action=create-auction" class="btn btn-primary">Create Auction</a>
        </div>
    <?php else: ?>
        <div class="auctions-grid" id="auctionsGrid">
            <?php foreach ($active_auctions as $auction): ?>
                <div class="auction-card" 
                     data-title="<?php echo strtolower(htmlspecialchars($auction['title'])); ?>"
                     data-category="<?php echo htmlspecialchars($auction['category']); ?>"
                     data-price="<?php echo $auction['current_price']; ?>"
                     data-bids="<?php echo $auction['bid_count']; ?>"
                     data-endtime="<?php echo strtotime($auction['end_time']); ?>"
                     data-created="<?php echo strtotime($auction['created_at']); ?>">
                    <div class="auction-image">
    <?php if (!empty($auction['image_url'])): ?>
        <?php 
        // Build correct URL - NO public folder in image path
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
        
        <!-- No Results Message -->
        <div id="noResults" class="no-results" style="display: none;">
            <i class="fas fa-search"></i>
            <h3>No auctions found</h3>
            <p>Try adjusting your search or filter criteria</p>
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

.filter-bar {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    align-items: center;
    background: var(--dark-elevated);
    padding: 1rem 1.5rem;
    border-radius: 60px;
    border: 1px solid var(--border-color);
}

.search-box {
    position: relative;
    flex: 2;
    min-width: 200px;
}

.search-box i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.9rem;
}

.search-box input {
    width: 100%;
    padding: 10px 16px 10px 40px;
    background: var(--dark-card);
    border: 1px solid var(--border-color);
    border-radius: 50px;
    color: var(--text-primary);
    font-size: 0.9rem;
    transition: var(--transition);
}

.search-box input:focus {
    outline: none;
    border-color: var(--primary-gold);
    box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.1);
}

.search-box input::placeholder {
    color: var(--text-muted);
}

.filter-group select {
    padding: 10px 16px;
    background: var(--dark-card);
    border: 1px solid var(--border-color);
    border-radius: 50px;
    color: var(--text-primary);
    font-size: 0.9rem;
    cursor: pointer;
    transition: var(--transition);
}

.filter-group select:focus {
    outline: none;
    border-color: var(--primary-gold);
}

.filter-results {
    margin-left: auto;
    color: var(--text-muted);
    font-size: 0.85rem;
    white-space: nowrap;
}

.filter-results span {
    color: var(--primary-gold);
    font-weight: 600;
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

.btn-block {
    display: block;
    width: 100%;
    text-align: center;
}

.btn-primary, .btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 28px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
    border: none;
    cursor: pointer;
    font-size: 14px;
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

.no-results {
    text-align: center;
    padding: 4rem;
    background: var(--dark-elevated);
    border-radius: 20px;
    margin-top: 2rem;
}

.no-results i {
    font-size: 4rem;
    color: var(--primary-gold);
    margin-bottom: 1rem;
}

.no-results h3 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.no-results p {
    color: var(--text-secondary);
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

@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }
    
    .dashboard-header {
        flex-direction: column;
        text-align: center;
    }
    
    .filter-bar {
        flex-direction: column;
        border-radius: 20px;
        align-items: stretch;
    }
    
    .search-box {
        width: 100%;
    }
    
    .filter-group select {
        width: 100%;
    }
    
    .filter-results {
        text-align: center;
        margin-left: 0;
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

function filterAuctions() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;
    const sortFilter = document.getElementById('sortFilter').value;
    
    const auctionCards = document.querySelectorAll('.auction-card');
    let visibleCount = 0;
    const auctionsArray = [];
    
    auctionCards.forEach(card => {
        const title = card.dataset.title;
        const category = card.dataset.category;
        const price = parseFloat(card.dataset.price);
        const bids = parseInt(card.dataset.bids);
        const endTime = parseInt(card.dataset.endtime);
        const created = parseInt(card.dataset.created);
        
        let show = true;
        
        if (searchTerm && !title.includes(searchTerm)) {
            show = false;
        }
        
        if (show && categoryFilter !== 'all' && category !== categoryFilter) {
            show = false;
        }
        
        auctionsArray.push({
            card: card,
            show: show,
            title: title,
            category: category,
            price: price,
            bids: bids,
            endTime: endTime,
            created: created
        });
    });
    
    auctionsArray.sort((a, b) => {
        switch(sortFilter) {
            case 'price_asc':
                return a.price - b.price;
            case 'price_desc':
                return b.price - a.price;
            case 'bids_desc':
                return b.bids - a.bids;
            case 'newest':
                return b.created - a.created;
            case 'time_asc':
            default:
                return a.endTime - b.endTime;
        }
    });
    
    const grid = document.getElementById('auctionsGrid');
    const noResults = document.getElementById('noResults');
    
    auctionsArray.forEach(item => {
        if (item.show) {
            item.card.style.display = 'block';
            visibleCount++;
        } else {
            item.card.style.display = 'none';
        }
        grid.appendChild(item.card);
    });
    
    document.getElementById('resultsCount').innerText = visibleCount;
    
    if (visibleCount === 0) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}

document.getElementById('searchInput').addEventListener('input', filterAuctions);
document.getElementById('categoryFilter').addEventListener('change', filterAuctions);
document.getElementById('sortFilter').addEventListener('change', filterAuctions);

initCountdowns();
filterAuctions();
</script>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>