<?php 
$page_title = "Admin - Edit User";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin'])) {
    header('Location: index.php?action=login');
    exit;
}

ob_start();
?>

<div class="form-container">
    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-user-edit"></i>
            <h2>Edit User (Admin)</h2>
            <p>Modify user details</p>
        </div>
        
        <form action="index.php?action=admin-update-user" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user-circle"></i> Full Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Address</label>
                <textarea name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-dollar-sign"></i> Balance</label>
                    <input type="number" name="balance" step="0.01" value="<?php echo $user['balance']; ?>">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-user-shield"></i> Admin Status</label>
                    <select name="is_admin">
                        <option value="0" <?php echo ($user['is_admin'] == 0) ? 'selected' : ''; ?>>Regular User</option>
                        <option value="1" <?php echo ($user['is_admin'] == 1) ? 'selected' : ''; ?>>Administrator</option>
                    </select>
                </div>
            </div>
            
            <div class="form-info">
                <h4><i class="fas fa-info-circle"></i> Important Notes</h4>
                <ul>
                    <li>Username and email cannot be changed</li>
                    <li>Balance changes will affect user's wallet</li>
                    <li>Making a user admin gives them full access</li>
                    <li>All changes are logged for security</li>
                </ul>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="index.php?action=admin-users" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.form-container {
    display: flex;
    justify-content: center;
    padding: 2rem;
}

.form-card {
    background: var(--dark-elevated);
    border-radius: 24px;
    padding: 2rem;
    max-width: 800px;
    width: 100%;
    border: 1px solid var(--border-color);
}

.form-header {
    text-align: center;
    margin-bottom: 2rem;
}

.form-header i {
    font-size: 3rem;
    color: var(--primary-gold);
    margin-bottom: 1rem;
}

.form-header h2 {
    color: var(--primary-gold);
    margin-bottom: 0.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
    font-weight: 500;
}

.form-group label i {
    color: var(--primary-gold);
    margin-right: 8px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    background: var(--dark-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    color: var(--text-primary);
    font-size: 14px;
}

.form-group input:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-info {
    margin-top: 2rem;
    padding: 1.2rem;
    background: var(--dark-card);
    border-radius: 16px;
    border: 1px solid var(--border-color);
}

.form-info h4 {
    color: var(--primary-gold);
    margin-bottom: 0.5rem;
}

.form-info ul {
    margin-left: 1.2rem;
    color: var(--text-secondary);
}

.form-info ul li {
    padding: 5px 0;
}

.button-group {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.btn-primary, .btn-secondary {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    border: none;
    font-size: 14px;
}

.btn-primary {
    background: var(--primary-gold);
    color: #000;
}

.btn-primary:hover {
    background: #ffed4a;
    transform: translateY(-2px);
}

.btn-secondary {
    background: transparent;
    color: var(--primary-gold);
    border: 1px solid var(--primary-gold);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-secondary:hover {
    background: rgba(255, 215, 0, 0.1);
}

@media (max-width: 768px) {
    .form-container {
        padding: 1rem;
    }
    
    .form-card {
        padding: 1.5rem;
    }
    
    .form-row {
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