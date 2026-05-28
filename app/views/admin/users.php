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
                    <i class="fas fa-store"></i> Marketplace
                </a>
            </nav>
        </div>
        
        <div class="admin-content">
            <!-- Search and Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search by username, email, or name...">
                </div>
                <div class="filter-group">
                    <select id="roleFilter">
                        <option value="all">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="user">Regular User</option>
                    </select>
                </div>
                <div class="filter-results">
                    <span id="resultsCount">0</span> users found
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="admin-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Full Name</th>
                            <th>Phone</th>
                            <th>Balance</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr data-username="<?php echo strtolower(htmlspecialchars($user['username'])); ?>"
                                data-email="<?php echo strtolower(htmlspecialchars($user['email'])); ?>"
                                data-name="<?php echo strtolower(htmlspecialchars($user['full_name'] ?? '')); ?>"
                                data-role="<?php echo $user['is_admin'] ? 'admin' : 'user'; ?>">
                                <td data-label="ID"><?php echo $user['id']; ?></td>
                                <td data-label="Username"><?php echo htmlspecialchars($user['username']); ?></td>
                                <td data-label="Email"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td data-label="Full Name"><?php echo htmlspecialchars($user['full_name'] ?? '-'); ?></td>
                                <td data-label="Phone"><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                <td data-label="Balance" class="price">₱<?php echo number_format($user['balance'], 2); ?></td>
                                <td data-label="Role">
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
            
            <div id="noResults" class="no-results" style="display: none;">
                <i class="fas fa-search"></i>
                <h3>No users found</h3>
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

/* Filter Bar */
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
    min-width: 900px;
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

.price {
    color: var(--primary-gold);
    font-weight: 600;
}

.admin-badge {
    background: rgba(255, 215, 0, 0.2);
    color: var(--primary-gold);
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 600;
}

.user-badge {
    background: rgba(108, 117, 125, 0.2);
    color: #6c757d;
    padding: 4px 12px;
    border-radius: 50px;
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
function filterUsers() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const roleFilter = document.getElementById('roleFilter').value;
    
    const rows = document.querySelectorAll('#usersTable tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const username = row.dataset.username || '';
        const email = row.dataset.email || '';
        const name = row.dataset.name || '';
        const role = row.dataset.role || '';
        
        let show = true;
        
        // Search filter
        if (searchTerm && !username.includes(searchTerm) && !email.includes(searchTerm) && !name.includes(searchTerm)) {
            show = false;
        }
        
        // Role filter
        if (show && roleFilter !== 'all' && role !== roleFilter) {
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

document.getElementById('searchInput').addEventListener('input', filterUsers);
document.getElementById('roleFilter').addEventListener('change', filterUsers);

// Initial count
filterUsers();
</script>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
$conn->close();
?>