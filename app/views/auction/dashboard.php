<?php 
$page_title = "Marketplace";

// Check if user is logged in
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
    
    <div class="filter-bar">
        <div class="filter-group">
            <select id="category-filter">
                <option value="">All Categories</option>
                <option value="electronics">Electronics</option>
                <option value="collectibles">Collectibles</option>
                <option value="art">Art</option>
                <option value="furniture">Furniture</option>
                <option value="fashion">Fashion</option>
                <option value="vehicles">Vehicles</option>
            </select>
        </div>
        <div class="filter-group">
            <select id="sort-filter">
                <option value="ending">Ending Soon</option>
                <option value="newest">Newest First</option>
                <option value="price-low">Price: Low to High</option>
                <option value="price-high">Price: High to Low</option>
                <option value="most-bids">Most Bids</option>
            </select>
        </div>
        <div class="filter-group search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search auctions...">
        </div>
    </div>
    
    <div class="auctions-grid">
        <!-- Active Auction Cards -->
        <div class="auction-card">
            <div class="auction-image">
                <img src="http://localhost/AzBuy/public/assets/img/rolex.jpg" alt="Vintage Rolex Watch" onerror="this.src='https://via.placeholder.com/300x200/1a1a1a/ffd700?text=No+Image'">
                <div class="auction-timer">
                    <i class="fas fa-hourglass-half"></i> <span class="countdown" data-time="86400">24h 00m 00s</span>
                </div>
                <div class="bid-count-badge">15 bids</div>
            </div>
            <div class="auction-info">
                <h3>Vintage Rolex Watch 1989</h3>
                <p class="seller">by <span>luxury_collector</span></p>
                <p class="category"><i class="fas fa-tag"></i> Collectibles</p>
                <div class="price-info">
                    <span class="current-price">$1,250.00</span>
                    <span class="starting-price">Start: $500</span>
                </div>
                <div class="bid-progress">
                    <div class="progress-bar" style="width: 65%"></div>
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
                <p class="seller">by <span>tech_haven</span></p>
                <p class="category"><i class="fas fa-tag"></i> Electronics</p>
                <div class="price-info">
                    <span class="current-price">$2,450.00</span>
                    <span class="starting-price">Start: $1,800</span>
                </div>
                <div class="bid-progress">
                    <div class="progress-bar" style="width: 45%"></div>
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
                <p class="seller">by <span>art_gallery</span></p>
                <p class="category"><i class="fas fa-tag"></i> Art</p>
                <div class="price-info">
                    <span class="current-price">$3,200.00</span>
                    <span class="starting-price">Start: $2,500</span>
                </div>
                <div class="bid-progress">
                    <div class="progress-bar" style="width: 80%"></div>
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
                <p class="seller">by <span>sneakerhead</span></p>
                <p class="category"><i class="fas fa-tag"></i> Fashion</p>
                <div class="price-info">
                    <span class="current-price">$890.00</span>
                    <span class="starting-price">Start: $400</span>
                </div>
                <div class="bid-progress">
                    <div class="progress-bar" style="width: 55%"></div>
                </div>
                <a href="index.php?action=view-auction&id=4" class="btn btn-primary btn-block">Place Bid</a>
            </div>
        </div>
    </div>
    
    <div class="pagination">
        <button class="page-btn active">1</button>
        <button class="page-btn">2</button>
        <button class="page-btn">3</button>
        <button class="page-btn">4</button>
        <button class="page-btn next">Next <i class="fas fa-chevron-right"></i></button>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>