<?php 
$page_title = "My Auctions";

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

// Get bid counts for each auction
require_once dirname(__DIR__) . '/../../config/Database.php';
$database = new Database();
$db_conn = $database->connect();

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
        <button class="tab-btn active" onclick="filterItems('all')">All (<?php echo count($my_auctions); ?>)</button>
        <button class="tab-btn" onclick="filterItems('active')">Active (<?php echo count(array_filter($my_auctions, function($a) { return $a['status'] == 'active'; })); ?>)</button>
        <button class="tab-btn" onclick="filterItems('ended')">Ended (<?php echo count(array_filter($my_auctions, function($a) { return $a['status'] == 'ended' || $a['status'] == 'payment_pending' || $a['status'] == 'paid'; })); ?>)</button>
    </div>
    
    <?php if (empty($my_auctions)): ?>
        <div class="empty-state">
            <i class="fas fa-gavel"></i>
            <h3>No Auctions Yet</h3>
            <p>You haven't created any auctions yet. Start selling now!</p>
            <a href="index.php?action=create-auction" class="btn btn-primary">Create Your First Auction</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Starting Price</th>
                        <th>Current Bid</th>
                        <th>Bids</th>
                        <th>Time Left</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($my_auctions as $auction): ?>
                        <?php 
                        // Get bid count for this auction
                        $bid_count = 0;
                        $stmt = $db_conn->prepare("SELECT COUNT(*) as count FROM bids WHERE auction_id = ?");
                        $stmt->bind_param("i", $auction['id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($row = $result->fetch_assoc()) {
                            $bid_count = $row['count'];
                        }
                        $stmt->close();
                        ?>
                        <tr class="<?php echo $auction['status'] == 'active' ? 'active-row' : 'ended-row'; ?>">
                            <td class="item-cell">
                                <div class="item-info">
                                    <?php if ($auction['image_url']): ?>
                                        <img src="/AzBuy/public/<?php echo $auction['image_url']; ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>" onerror="this.src='https://via.placeholder.com/50x50/1a1a1a/ffd700?text=No+Image'">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/50x50/1a1a1a/ffd700?text=<?php echo urlencode(substr($auction['title'], 0, 10)); ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>">
                                    <?php endif; ?>
                                    <div>
                                        <strong><?php echo htmlspecialchars($auction['title']); ?></strong>
                                        <small>Listed on <?php echo date('M d, Y', strtotime($auction['created_at'])); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($auction['category']); ?></td>
                            <td class="price">$<?php echo number_format($auction['starting_price'], 2); ?></td>
                            <td class="price">$<?php echo number_format($auction['current_price'], 2); ?></td>
                            <td><?php echo $bid_count; ?> bids</td>
                            <td>
                                <?php if ($auction['status'] == 'active'): ?>
                                    <?php
                                    $time_left = strtotime($auction['end_time']) - time();
                                    if ($time_left > 0):
                                        $days = floor($time_left / 86400);
                                        $hours = floor(($time_left % 86400) / 3600);
                                        $minutes = floor(($time_left % 3600) / 60);
                                    ?>
                                        <span class="time-left">
                                            <?php if ($days > 0): ?><?php echo $days; ?>d <?php endif; ?>
                                            <?php echo $hours; ?>h <?php echo $minutes; ?>m
                                        </span>
                                    <?php else: ?>
                                        <span class="time-left ended">Ended</span>
                                    <?php endif; ?>
                                <?php elseif ($auction['status'] == 'payment_pending'): ?>
                                    <span class="time-left pending">Awaiting Payment</span>
                                <?php elseif ($auction['status'] == 'paid'): ?>
                                    <span class="time-left paid">Paid ✓</span>
                                <?php else: ?>
                                    <span class="time-left ended">Ended</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($auction['status'] == 'active'): ?>
                                    <span class="status-badge active">Active</span>
                                <?php elseif ($auction['status'] == 'payment_pending'): ?>
                                    <span class="status-badge payment_pending">Payment Pending</span>
                                <?php elseif ($auction['status'] == 'paid'): ?>
                                    <span class="status-badge paid">Paid</span>
                                <?php else: ?>
                                    <span class="status-badge ended">Ended</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions-cell">
                                <a href="index.php?action=view-auction&id=<?php echo $auction['id']; ?>" class="btn-icon view" title="View Auction">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($auction['status'] == 'active'): ?>
                                    <a href="index.php?action=delete-auction&id=<?php echo $auction['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete this auction? This action cannot be undone.')" title="Delete Auction">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($auction['status'] == 'ended' && isset($auction['winner_id']) && $auction['winner_id'] == $_SESSION['user_id']): ?>
                                    <span class="winner-badge">You won!</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($my_auctions)): ?>
    <div class="analytics-section">
        <h3><i class="fas fa-chart-line"></i> Auction Analytics</h3>
        <div class="analytics-grid">
            <div class="analytics-card">
                <span class="label">Total Listings</span>
                <span class="value"><?php echo count($my_auctions); ?></span>
            </div>
            <div class="analytics-card">
                <span class="label">Active Listings</span>
                <span class="value">
                    <?php echo count(array_filter($my_auctions, function($a) { return $a['status'] == 'active'; })); ?>
                </span>
            </div>
            <div class="analytics-card">
                <span class="label">Completed Sales</span>
                <span class="value">
                    <?php echo count(array_filter($my_auctions, function($a) { return $a['status'] == 'paid'; })); ?>
                </span>
            </div>
            <div class="analytics-card">
                <span class="label">Pending Payment</span>
                <span class="value">
                    <?php echo count(array_filter($my_auctions, function($a) { return $a['status'] == 'payment_pending'; })); ?>
                </span>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.my-items-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.my-items-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.my-items-header h1 {
    color: var(--primary-gold);
    margin-bottom: 0.5rem;
}

.my-items-header p {
    color: var(--text-secondary);
}

.items-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 0.5rem;
}

.tab-btn {
    padding: 8px 24px;
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition);
    font-size: 14px;
    font-weight: 500;
    border-radius: 50px;
}

.tab-btn:hover,
.tab-btn.active {
    color: var(--primary-gold);
    background: rgba(255, 215, 0, 0.1);
}

.table-responsive {
    overflow-x: auto;
    background: var(--dark-elevated);
    border-radius: 20px;
    border: 1px solid var(--border-color);
}

.items-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

.items-table th {
    background: rgba(255, 215, 0, 0.1);
    color: var(--primary-gold);
    padding: 1rem;
    text-align: left;
    font-weight: 600;
}

.items-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-secondary);
}

.items-table tr:hover {
    background: rgba(255, 215, 0, 0.05);
}

.item-cell .item-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.item-cell .item-info img {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    object-fit: cover;
}

.item-cell .item-info strong {
    display: block;
    color: var(--text-primary);
}

.item-cell .item-info small {
    font-size: 11px;
    color: var(--text-muted);
}

.price {
    font-weight: 700;
    color: var(--primary-gold);
}

.time-left {
    font-size: 13px;
    color: var(--text-secondary);
}

.time-left.ended {
    color: #dc3545;
}

.time-left.pending {
    color: #ffc107;
}

.time-left.paid {
    color: #28a745;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge.active {
    background: rgba(40, 167, 69, 0.15);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.status-badge.ended {
    background: rgba(108, 117, 125, 0.15);
    color: #6c757d;
    border: 1px solid rgba(108, 117, 125, 0.3);
}

.status-badge.payment_pending {
    background: rgba(255, 193, 7, 0.15);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.status-badge.paid {
    background: rgba(40, 167, 69, 0.15);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.actions-cell {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-icon {
    padding: 6px 10px;
    border-radius: 6px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: var(--transition);
}

.btn-icon.view {
    background: rgba(23, 162, 184, 0.2);
    color: #17a2b8;
}

.btn-icon.delete {
    background: rgba(220, 53, 69, 0.2);
    color: #dc3545;
}

.btn-icon:hover {
    transform: translateY(-2px);
    filter: brightness(1.1);
}

.winner-badge {
    background: var(--primary-gold);
    color: #000;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: bold;
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

.analytics-section {
    margin-top: 3rem;
    padding: 2rem;
    background: var(--dark-elevated);
    border-radius: 20px;
    border: 1px solid var(--border-color);
}

.analytics-section h3 {
    color: var(--primary-gold);
    margin-bottom: 1rem;
}

.analytics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.analytics-card {
    padding: 1rem;
    background: var(--dark-card);
    border-radius: 12px;
    text-align: center;
}

.analytics-card .label {
    font-size: 12px;
    color: var(--text-muted);
    display: block;
}

.analytics-card .value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-gold);
    display: block;
}

/* Responsive */
@media (max-width: 768px) {
    .my-items-container {
        padding: 1rem;
    }
    
    .my-items-header {
        flex-direction: column;
        text-align: center;
    }
    
    .items-tabs {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .tab-btn {
        padding: 6px 16px;
        font-size: 12px;
    }
    
    .items-table th,
    .items-table td {
        padding: 0.75rem;
        font-size: 12px;
    }
    
    .item-cell .item-info img {
        width: 40px;
        height: 40px;
    }
    
    .actions-cell {
        flex-direction: column;
    }
    
    .analytics-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function filterItems(status) {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        if (status === 'all') {
            row.style.display = '';
        } else if (status === 'active') {
            row.style.display = row.classList.contains('active-row') ? '' : 'none';
        } else if (status === 'ended') {
            row.style.display = row.classList.contains('ended-row') ? '' : 'none';
        }
    });
    
    document.querySelectorAll('.items-tabs .tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
}
</script>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
$db_conn->close();
?>