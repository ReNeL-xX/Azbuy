<?php 
$page_title = "Admin - Manage Auctions";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin'])) {
    header('Location: index.php?action=login');
    exit;
}

// Hardcode base URL for Hostinger
$base_url = 'https://azbuy.bsit2a.com/public';

// Create a new database connection for the view
require_once dirname(__DIR__) . '/../../config/Database.php';
$db_conn = new Database();
$conn = $db_conn->connect();

ob_start();
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-gavel"></i> Manage Auctions</h1>
        <p>View, edit, and delete all auctions</p>
    </div>
    
    <div class="admin-grid">
        <div class="admin-sidebar">
            <nav class="admin-nav">
                <a href="index.php?action=admin-dashboard">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="index.php?action=admin-auctions" class="active">
                    <i class="fas fa-gavel"></i> Manage Auctions
                </a>
                <a href="index.php?action=admin-users">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <a href="index.php?action=dashboard">
                    <i class="fas fa-store"></i> Marketplace
                </a>
            </nav>
        </div>
        
        <div class="admin-content">
            <!-- Search and Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search by title or seller...">
                </div>
                <div class="filter-group">
                    <select id="statusFilter">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="ended">Ended</option>
                        <option value="payment_pending">Payment Pending</option>
                        <option value="paid">Paid</option>
                    </select>
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
                        <option value="Sports">Sports</option>
                        <option value="Books">Books</option>
                        <option value="Music">Music</option>
                        <option value="Toys">Toys</option>
                    </select>
                </div>
                <div class="filter-results">
                    <span id="resultsCount">0</span> auctions found
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="admin-table" id="auctionsTable">
                    <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 8%;">Image</th>
                            <th style="width: 20%;">Title</th>
                            <th style="width: 12%;">Seller</th>
                            <th style="width: 10%;">Starting Price</th>
                            <th style="width: 10%;">Current Price</th>
                            <th style="width: 5%;">Bids</th>
                            <th style="width: 12%;">Status</th>
                            <th style="width: 10%;">Created</th>
                            <th style="width: 8%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($auctions as $auction): ?>
                            <?php 
                            $bid_count = 0;
                            $bid_stmt = $conn->prepare("SELECT COUNT(*) as count FROM bids WHERE auction_id = ?");
                            $bid_stmt->bind_param("i", $auction['id']);
                            $bid_stmt->execute();
                            $bid_result = $bid_stmt->get_result();
                            if ($bid_row = $bid_result->fetch_assoc()) {
                                $bid_count = $bid_row['count'];
                            }
                            $bid_stmt->close();
                            ?>
                            <tr data-title="<?php echo strtolower(htmlspecialchars($auction['title'])); ?>"
                                data-seller="<?php echo strtolower(htmlspecialchars($auction['seller_name'])); ?>"
                                data-status="<?php echo $auction['status']; ?>"
                                data-category="<?php echo $auction['category']; ?>">
                                <td data-label="ID"><?php echo $auction['id']; ?></td>
                               <td data-label="Image">
    <?php if (!empty($auction['image_url'])): ?>
        <?php 
        // Remove any leading slashes or public references
        $clean_url = ltrim($auction['image_url'], '/');
        $clean_url = str_replace('public/', '', $clean_url);
        // Build correct URL - NO /public in the path
        $image_src = 'https://azbuy.bsit2a.com/' . $clean_url;
        ?>
        <img src="<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>" class="auction-image" onerror="this.style.display='none'">
    <?php else: ?>
        <div class="no-image">
            <i class="fas fa-image"></i>
        </div>
    <?php endif; ?>
</td>
                                <td data-label="Title"><?php echo htmlspecialchars(substr($auction['title'], 0, 40)); ?>...</td>
                                <td data-label="Seller"><?php echo htmlspecialchars($auction['seller_name']); ?></td>
                                <td data-label="Starting Price" class="price">₱<?php echo number_format($auction['starting_price'], 2); ?></td>
                                <td data-label="Current Price" class="price">₱<?php echo number_format($auction['current_price'], 2); ?></td>
                                <td data-label="Bids" class="center"><?php echo $bid_count; ?></td>
                                <td data-label="Status">
                                    <span class="status-badge <?php echo $auction['status']; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $auction['status'])); ?>
                                    </span>
                                </td>
                                <td data-label="Created"><?php echo date('M d, Y', strtotime($auction['created_at'])); ?></td>
                                <td data-label="Actions" class="actions-cell">
                                    <a href="index.php?action=admin-edit-auction&id=<?php echo $auction['id']; ?>" class="btn-icon edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?action=admin-delete-auction&id=<?php echo $auction['id']; ?>" class="btn-icon delete" onclick="return confirm('Are you sure you want to delete this auction? This action cannot be undone.')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <a href="index.php?action=view-auction&id=<?php echo $auction['id']; ?>" class="btn-icon view" title="View" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div id="noResults" class="no-results" style="display: none;">
                <i class="fas fa-search"></i>
                <h3>No auctions found</h3>
                <p>Try adjusting your search or filter criteria</p>
            </div>
        </div>
    </div>
</div>

<style>
.admin-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.admin-header {
    margin-bottom: 2rem;
}

.admin-header h1 {
    color: var(--primary-gold);
    margin-bottom: 0.5rem;
}

.admin-header p {
    color: var(--text-secondary);
}

.admin-grid {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 2rem;
}

.admin-sidebar {
    background: var(--dark-elevated);
    border-radius: 20px;
    padding: 1.5rem;
    border: 1px solid var(--border-color);
    height: fit-content;
    position: sticky;
    top: 100px;
}

.admin-nav a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: var(--text-secondary);
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.3s;
    margin-bottom: 0.5rem;
}

.admin-nav a:hover,
.admin-nav a.active {
    background: rgba(255, 215, 0, 0.1);
    color: var(--primary-gold);
}

.admin-nav a i {
    width: 22px;
}

.admin-content {
    background: var(--dark-elevated);
    border-radius: 20px;
    padding: 1.5rem;
    border: 1px solid var(--border-color);
}

.filter-bar {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    align-items: center;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.search-box {
    position: relative;
    flex: 2;
    min-width: 200px;
}

.search-box i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.9rem;
}

.search-box input {
    width: 100%;
    padding: 10px 16px 10px 38px;
    background: var(--dark-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    color: var(--text-primary);
    font-size: 0.85rem;
    transition: all 0.3s;
}

.search-box input:focus {
    outline: none;
    border-color: var(--primary-gold);
}

.filter-group select {
    padding: 10px 16px;
    background: var(--dark-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    color: var(--text-primary);
    font-size: 0.85rem;
    cursor: pointer;
}

.filter-results {
    margin-left: auto;
    color: var(--text-muted);
    font-size: 0.8rem;
}

.filter-results span {
    color: var(--primary-gold);
    font-weight: 600;
}

.table-responsive {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1000px;
}

.admin-table th {
    background: rgba(255, 215, 0, 0.08);
    color: var(--primary-gold);
    padding: 14px 12px;
    text-align: left;
    font-weight: 600;
    font-size: 0.85rem;
}

.admin-table td {
    padding: 12px;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-secondary);
    vertical-align: middle;
    font-size: 0.85rem;
}

.admin-table tr:hover {
    background: rgba(255, 215, 0, 0.03);
}

.auction-image {
    width: 45px;
    height: 45px;
    object-fit: cover;
    border-radius: 8px;
}

.no-image {
    width: 45px;
    height: 45px;
    background: var(--dark-card);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
}

.price {
    color: var(--primary-gold);
    font-weight: 600;
}

.center {
    text-align: center;
}

.status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 600;
    text-align: center;
    min-width: 110px;
}

.status-badge.active {
    background: rgba(40, 167, 69, 0.15);
    color: #28a745;
}

.status-badge.ended {
    background: rgba(108, 117, 125, 0.15);
    color: #6c757d;
}

.status-badge.payment_pending {
    background: rgba(255, 193, 7, 0.15);
    color: #ffc107;
}

.status-badge.paid {
    background: rgba(40, 167, 69, 0.15);
    color: #28a745;
}

.actions-cell {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-icon {
    padding: 6px 10px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    width: 32px;
    height: 32px;
}

.btn-icon.edit {
    background: rgba(255, 193, 7, 0.15);
    color: #ffc107;
}

.btn-icon.delete {
    background: rgba(220, 53, 69, 0.15);
    color: #dc3545;
}

.btn-icon.view {
    background: rgba(23, 162, 184, 0.15);
    color: #17a2b8;
}

.btn-icon:hover {
    transform: translateY(-2px);
    filter: brightness(1.1);
}

.no-results {
    text-align: center;
    padding: 3rem;
    color: var(--text-muted);
}

.no-results i {
    font-size: 3rem;
    color: var(--primary-gold);
    margin-bottom: 1rem;
}

.no-results h3 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

@media (max-width: 1024px) {
    .admin-grid {
        grid-template-columns: 1fr;
    }
    
    .admin-sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .admin-container {
        padding: 1rem;
    }
    
    .filter-bar {
        flex-direction: column;
    }
    
    .search-box {
        width: 100%;
    }
    
    .filter-group select {
        width: 100%;
    }
    
    .filter-results {
        margin-left: 0;
        text-align: center;
    }
    
    .admin-table th,
    .admin-table td {
        padding: 8px;
        font-size: 12px;
    }
}
</style>

<script>
function filterAuctions() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    
    const rows = document.querySelectorAll('#auctionsTable tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const title = row.dataset.title || '';
        const seller = row.dataset.seller || '';
        const status = row.dataset.status || '';
        const category = row.dataset.category || '';
        
        let show = true;
        
        if (searchTerm && !title.includes(searchTerm) && !seller.includes(searchTerm)) {
            show = false;
        }
        
        if (show && statusFilter !== 'all' && status !== statusFilter) {
            show = false;
        }
        
        if (show && categoryFilter !== 'all' && category !== categoryFilter) {
            show = false;
        }
        
        if (show) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    document.getElementById('resultsCount').innerText = visibleCount;
    
    const noResults = document.getElementById('noResults');
    if (visibleCount === 0) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}

document.getElementById('searchInput').addEventListener('input', filterAuctions);
document.getElementById('statusFilter').addEventListener('change', filterAuctions);
document.getElementById('categoryFilter').addEventListener('change', filterAuctions);

filterAuctions();
</script>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
$conn->close();
?>