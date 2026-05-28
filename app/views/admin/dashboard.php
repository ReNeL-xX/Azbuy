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
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    </div>
    
    <div class="admin-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($stats['total_users']); ?></h3>
                <p>Total Users</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-gavel"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($stats['total_auctions']); ?></h3>
                <p>Total Auctions</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($stats['active_auctions']); ?></h3>
                <p>Active Auctions</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-gavel"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($stats['total_bids']); ?></h3>
                <p>Total Bids</p>
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
                    <i class="fas fa-store"></i> Marketplace
                </a>
            </nav>
        </div>
        
        <div class="admin-content">
            <div class="admin-section">
                <div class="section-header">
                    <h2><i class="fas fa-clock"></i> Recent Auctions</h2>
                </div>
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
                                    <td class="title-cell"><?php echo htmlspecialchars(substr($auction['title'], 0, 40)); ?>...</td>
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
                <div class="section-header">
                    <h2><i class="fas fa-users"></i> Recent Users</h2>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Balance</th>
                                <th>Joined</th>
                                <th>Role</th>
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
                                    <td><?php echo $user['is_admin'] ? '<span class="admin-badge">Admin</span>' : '<span class="user-badge">User</span>'; ?></td>
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
    font-size: 1.8rem;
}

.admin-header p {
    color: var(--text-secondary);
}

.admin-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--dark-elevated);
    padding: 1.5rem;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 1.2rem;
    border: 1px solid var(--border-color);
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary-gold);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.stat-icon i {
    font-size: 2.5rem;
    color: var(--primary-gold);
}

.stat-info h3 {
    font-size: 1.8rem;
    color: var(--primary-gold);
    margin: 0;
    font-weight: 700;
}

.stat-info p {
    color: var(--text-muted);
    margin: 0;
    font-size: 0.85rem;
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
    font-size: 1.1rem;
}

.admin-content {
    background: var(--dark-elevated);
    border-radius: 20px;
    padding: 1.5rem;
    border: 1px solid var(--border-color);
}

.admin-section {
    margin-bottom: 2rem;
}

.admin-section:last-child {
    margin-bottom: 0;
}

.section-header {
    margin-bottom: 1rem;
}

.section-header h2 {
    color: var(--primary-gold);
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.table-responsive {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th {
    background: rgba(255, 215, 0, 0.08);
    color: var(--primary-gold);
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 0.85rem;
}

.admin-table td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-secondary);
    font-size: 0.85rem;
}

.admin-table tr:hover {
    background: rgba(255, 215, 0, 0.03);
}

.title-cell {
    max-width: 250px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.price {
    color: var(--primary-gold);
    font-weight: 600;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 11px;
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

.admin-badge {
    background: rgba(255, 215, 0, 0.2);
    color: var(--primary-gold);
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 600;
}

.user-badge {
    background: rgba(108, 117, 125, 0.2);
    color: #6c757d;
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 600;
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
    
    .admin-stats {
        grid-template-columns: 1fr;
    }
    
    .admin-table th,
    .admin-table td {
        padding: 8px 12px;
        font-size: 12px;
    }
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>