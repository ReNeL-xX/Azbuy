<?php 
$page_title = "My Auctions";

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

ob_start();
?>

<div class="my-items-container">
    <div class="my-items-header">
        <div>
            <h1><i class="fas fa-gavel"></i> My Auctions</h1>
            <p>Manage all your auction listings</p>
        </div>
        <a href="index.php?action=create-auction" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Auction
        </a>
    </div>
    
    <div class="items-tabs">
        <button class="tab-btn active" onclick="filterItems('active')">Active (3)</button>
        <button class="tab-btn" onclick="filterItems('ended')">Ended (2)</button>
        <button class="tab-btn" onclick="filterItems('draft')">Drafts (0)</button>
    </div>
    
    <div class="items-table">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Current Bid</th>
                    <th>Bids</th>
                    <th>Time Left</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr class="active-row">
                    <td class="item-cell">
                        <div class="item-info">
                             <img src="http://localhost/AzBuy/public/assets/img/rolex.jpg" alt="MacBook Pro M3 Max" onerror="this.src='https://via.placeholder.com/300x200/1a1a1a/ffd700?text=No+Image'">
                            <div>
                                <strong>Vintage Rolex Watch</strong>
                                <small>Listed on Dec 10, 2024</small>
                            </div>
                        </div>
                    </td>
                    <td>Collectibles</td>
                    <td class="price">$1,250.00</td>
                    <td>15 bids</td>
                    <td><span class="countdown-small" data-time="86400">24h 00m</span></td>
                    <td><span class="status-badge active">Active</span></td>
                    <td class="actions">
                        <a href="index.php?action=view-auction&id=1" class="btn-icon view"><i class="fas fa-eye"></i></a>
                        <a href="#" class="btn-icon edit"><i class="fas fa-edit"></i></a>
                        <a href="#" class="btn-icon delete" onclick="return confirm('Delete this auction?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <tr class="active-row">
                    <td class="item-cell">
                        <div class="item-info">
                             <img src="http://localhost/AzBuy/public/assets/img/MacBookProM3Max.jpg" alt="MacBook Pro M3 Max" onerror="this.src='https://via.placeholder.com/300x200/1a1a1a/ffd700?text=No+Image'">
                            <div>
                                <strong>MacBook Pro M3</strong>
                                <small>Listed on Dec 12, 2024</small>
                            </div>
                        </div>
                    </td>
                    <td>Electronics</td>
                    <td class="price">$2,450.00</td>
                    <td>32 bids</td>
                    <td><span class="countdown-small" data-time="172800">48h 00m</span></td>
                    <td><span class="status-badge active">Active</span></td>
                    <td class="actions">
                        <a href="#" class="btn-icon view"><i class="fas fa-eye"></i></a>
                        <a href="#" class="btn-icon edit"><i class="fas fa-edit"></i></a>
                        <a href="#" class="btn-icon delete"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <tr class="ended-row">
                    <td class="item-cell">
                        <div class="item-info">
                             <img src="http://localhost/AzBuy/public/assets/img/OriginalAbstractPainting.jpg" alt="Original Abstract Painting" onerror="this.src='https://via.placeholder.com/300x200/1a1a1a/ffd700?text=No+Image'">
                            <div>
                                <strong>Abstract Painting</strong>
                                <small>Listed on Nov 25, 2024</small>
                            </div>
                        </div>
                    </td>
                    <td>Art</td>
                    <td class="price">$3,200.00</td>
                    <td>8 bids</td>
                    <td>Ended</td>
                    <td><span class="status-badge ended">Ended</span></td>
                    <td class="actions">
                        <a href="#" class="btn-icon view"><i class="fas fa-eye"></i></a>
                        <a href="#" class="btn-icon relist">Relist</a>
                    </td>
                </tr>
                <tr class="ended-row">
                    <td class="item-cell">
                        <div class="item-info">
                             <img src="http://localhost/AzBuy/public/assets/img/NikeAirJordan1Limited.jpg" alt="Nike Air Jordan 1" onerror="this.src='https://via.placeholder.com/300x200/1a1a1a/ffd700?text=No+Image'">
                            <div>
                                <strong>Nike Air Jordan 1</strong>
                                <small>Listed on Nov 20, 2024</small>
                            </div>
                        </div>
                    </td>
                    <td>Fashion</td>
                    <td class="price">$890.00</td>
                    <td>23 bids</td>
                    <td>Ended</td>
                    <td><span class="status-badge ended">Ended - Won</span></td>
                    <td class="actions">
                        <a href="#" class="btn-icon view"><i class="fas fa-eye"></i></a>
                        <a href="#" class="btn-icon relist">Relist</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="analytics-section">
        <h3><i class="fas fa-chart-line"></i> Auction Analytics</h3>
        <div class="analytics-grid">
            <div class="analytics-card">
                <span class="label">Total Sales</span>
                <span class="value">$5,740.00</span>
                <span class="trend up"><i class="fas fa-arrow-up"></i> +23%</span>
            </div>
            <div class="analytics-card">
                <span class="label">Active Listings</span>
                <span class="value">2</span>
                <span class="trend down"><i class="fas fa-arrow-down"></i> -1</span>
            </div>
            <div class="analytics-card">
                <span class="label">Total Bids Received</span>
                <span class="value">78</span>
                <span class="trend up"><i class="fas fa-arrow-up"></i> +15%</span>
            </div>
            <div class="analytics-card">
                <span class="label">Conversion Rate</span>
                <span class="value">68%</span>
                <span class="trend up"><i class="fas fa-arrow-up"></i> +5%</span>
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