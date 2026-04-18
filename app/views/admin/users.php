<?php 
$page_title = "Admin - Manage Users";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin'])) {
    header('Location: index.php?action=login');
    exit;
}

// Create a new database connection for the view
require_once dirname(__DIR__) . '/../../config/Database.php';
$db_conn = new Database();
$conn = $db_conn->connect();

ob_start();
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-users"></i> Manage Users</h1>
        <p>View, edit, and manage all users</p>
    </div>
    
    <div class="admin-grid">
        <div class="admin-sidebar">
            <nav class="admin-nav">
                <a href="index.php?action=admin-dashboard">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="index.php?action=admin-auctions">
                    <i class="fas fa-gavel"></i> Manage Auctions
                </a>
                <a href="index.php?action=admin-users" class="active">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <a href="index.php?action=dashboard">
                    <i class="fas fa-store"></i> Go to Marketplace
                </a>
            </nav>
        </div>
        
        <div class="admin-content">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Full Name</th>
                            <th>Phone</th>
                            <th>Balance</th>
                            <th>Admin</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td data-label="ID"><?php echo $user['id']; ?></td>
                                <td data-label="Username"><?php echo htmlspecialchars($user['username']); ?></td>
                                <td data-label="Email"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td data-label="Full Name"><?php echo htmlspecialchars($user['full_name'] ?? '-'); ?></td>
                                <td data-label="Phone"><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                <td data-label="Balance" class="price">$<?php echo number_format($user['balance'], 2); ?></td>
                                <td data-label="Admin">
                                    <?php if ($user['is_admin'] == 1): ?>
                                        <span class="admin-badge">Admin</span>
                                    <?php else: ?>
                                        <span class="user-badge">User</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Joined"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td data-label="Actions" class="actions-cell">
                                    <a href="index.php?action=admin-edit-user&id=<?php echo $user['id']; ?>" class="btn-icon edit" title="Edit User">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="index.php?action=admin-delete-user&id=<?php echo $user['id']; ?>" class="btn-icon delete" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')" title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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

.table-responsive {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.admin-table th {
    background: rgba(255, 215, 0, 0.1);
    color: var(--primary-gold);
    padding: 14px 12px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
}

.admin-table td {
    padding: 12px;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-secondary);
    vertical-align: middle;
    font-size: 14px;
}

.admin-table tr:hover {
    background: rgba(255, 215, 0, 0.05);
}

.price {
    color: var(--primary-gold);
    font-weight: bold;
}

.admin-badge {
    background: rgba(255, 215, 0, 0.2);
    color: var(--primary-gold);
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}

.user-badge {
    background: rgba(108, 117, 125, 0.2);
    color: #6c757d;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
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
    justify-content: center;
    transition: var(--transition);
    width: 32px;
    height: 32px;
}

.btn-icon.edit {
    background: rgba(255, 193, 7, 0.2);
    color: #ffc107;
}

.btn-icon.delete {
    background: rgba(220, 53, 69, 0.2);
    color: #dc3545;
}

.btn-icon.view {
    background: rgba(23, 162, 184, 0.2);
    color: #17a2b8;
}

.btn-icon:hover {
    transform: translateY(-2px);
    filter: brightness(1.1);
}

@media (max-width: 768px) {
    .admin-container {
        padding: 1rem;
    }
    
    .admin-grid {
        grid-template-columns: 1fr;
    }
    
    .admin-table {
        min-width: 800px;
    }
    
    .admin-table th,
    .admin-table td {
        padding: 10px 8px;
        font-size: 12px;
    }
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
$conn->close();
?>