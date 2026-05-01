<?php 
$page_title = "Admin Dashboard";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin'])) {
    header('Location: index.php?action=login');
    exit;
}

ob_start();
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-user-shield"></i> Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    </div>
    
    <div class="admin-stats">
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <div>
                <h3><?php echo number_format($stats['total_users']); ?></h3>
                <p>Total Users</p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-gavel"></i>
            <div>
                <h3><?php echo number_format($stats['total_auctions']); ?></h3>
                <p>Total Auctions</p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-clock"></i>
            <div>
                <h3><?php echo number_format($stats['active_auctions']); ?></h3>
                <p>Active Auctions</p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-hand-holding-usd"></i>
            <div>
                <h3><?php echo number_format($stats['total_bids']); ?></h3>
                <p>Total Bids</p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-dollar-sign"></i>
            <div>
                <h3>₱<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>
    </div>
    
    <div class="admin-grid">
        <div class="admin-sidebar">
            <nav class="admin-nav">
                <a href="index.php?action=admin-dashboard" class="active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="index.php?action=admin-auctions">
                    <i class="fas fa-gavel"></i> Manage Auctions
                </a>
                <a href="index.php?action=admin-users">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <a href="index.php?action=dashboard">
                    <i class="fas fa-store"></i> Go to Marketplace
                </a>
                
            </nav>
        </div>
        
        <div class="admin-content">
            <div class="admin-section">
                <h2>Recent Auctions</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Seller</th>
                                <th>Current Price</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_auctions as $auction): ?>
                                <tr>
                                    <td><?php echo $auction['id']; ?></td>
                                    <td><?php echo htmlspecialchars(substr($auction['title'], 0, 30)); ?>...</td>
                                    <td><?php echo htmlspecialchars($auction['seller_name']); ?></td>
                                    <td class="price">₱<?php echo number_format($auction['current_price'], 2); ?></td>
                                    <td><span class="status-badge <?php echo $auction['status']; ?>"><?php echo ucfirst($auction['status']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($auction['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="admin-section">
                <h2>Recent Users</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Balance</th>
                                <th>Joined</th>
                                <th>Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td class="price">₱<?php echo number_format($user['balance'], 2); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td><?php echo $user['is_admin'] ? '<span class="admin-badge">Yes</span>' : '<span class="user-badge">No</span>'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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

.admin-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--dark-elevated);
    padding: 1.5rem;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid var(--border-color);
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary-gold);
}

.stat-card i {
    font-size: 2.5rem;
    color: var(--primary-gold);
}

.stat-card h3 {
    font-size: 1.8rem;
    color: var(--primary-gold);
    margin: 0;
}

.stat-card p {
    color: var(--text-muted);
    margin: 0;
}

.admin-grid {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 2rem;
}

.admin-sidebar {
    background: var(--dark-elevated);
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid var(--border-color);
    height: fit-content;
}

.admin-nav a {
    display: block;
    padding: 12px;
    color: var(--text-secondary);
    text-decoration: none;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    transition: var(--transition);
}

.admin-nav a:hover,
.admin-nav a.active {
    background: rgba(255, 215, 0, 0.1);
    color: var(--primary-gold);
}

.admin-nav a i {
    margin-right: 10px;
    width: 20px;
}

.admin-content {
    background: var(--dark-elevated);
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid var(--border-color);
}

.admin-section {
    margin-bottom: 2rem;
}

.admin-section h2 {
    color: var(--primary-gold);
    margin-bottom: 1rem;
    font-size: 1.3rem;
}

.table-responsive {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th {
    background: rgba(255, 215, 0, 0.1);
    color: var(--primary-gold);
    padding: 12px;
    text-align: left;
    font-weight: 600;
}

.admin-table td {
    padding: 12px;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-secondary);
}

.admin-table tr:hover {
    background: rgba(255, 215, 0, 0.05);
}

.price {
    color: var(--primary-gold);
    font-weight: bold;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
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

.admin-badge {
    background: rgba(255, 215, 0, 0.2);
    color: var(--primary-gold);
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
}

.user-badge {
    background: rgba(108, 117, 125, 0.2);
    color: #6c757d;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
}

@media (max-width: 768px) {
    .admin-container {
        padding: 1rem;
    }
    
    .admin-grid {
        grid-template-columns: 1fr;
    }
    
    .admin-stats {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>