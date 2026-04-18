<?php 
$page_title = "Admin - Edit Auction";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin'])) {
    header('Location: index.php?action=login');
    exit;
}

ob_start();
?>

<div class="form-container">
    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-edit"></i>
            <h2>Edit Auction (Admin)</h2>
            <p>Modify auction details - Status cannot be edited manually</p>
        </div>
        
        <form action="index.php?action=admin-update-auction" method="POST">
            <input type="hidden" name="auction_id" value="<?php echo $auction['id']; ?>">
            
            <div class="form-group">
                <label><i class="fas fa-heading"></i> Title</label>
                <input type="text" name="title" required value="<?php echo htmlspecialchars($auction['title']); ?>">
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Description</label>
                <textarea name="description" rows="5" required><?php echo htmlspecialchars($auction['description']); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Category</label>
                    <select name="category" required>
                        <option value="Electronics" <?php echo $auction['category'] == 'Electronics' ? 'selected' : ''; ?>>Electronics</option>
                        <option value="Collectibles" <?php echo $auction['category'] == 'Collectibles' ? 'selected' : ''; ?>>Collectibles</option>
                        <option value="Art" <?php echo $auction['category'] == 'Art' ? 'selected' : ''; ?>>Art</option>
                        <option value="Furniture" <?php echo $auction['category'] == 'Furniture' ? 'selected' : ''; ?>>Furniture</option>
                        <option value="Vehicles" <?php echo $auction['category'] == 'Vehicles' ? 'selected' : ''; ?>>Vehicles</option>
                        <option value="Fashion" <?php echo $auction['category'] == 'Fashion' ? 'selected' : ''; ?>>Fashion</option>
                        <option value="Jewelry" <?php echo $auction['category'] == 'Jewelry' ? 'selected' : ''; ?>>Jewelry</option>
                        <option value="Sports" <?php echo $auction['category'] == 'Sports' ? 'selected' : ''; ?>>Sports</option>
                        <option value="Books" <?php echo $auction['category'] == 'Books' ? 'selected' : ''; ?>>Books</option>
                        <option value="Music" <?php echo $auction['category'] == 'Music' ? 'selected' : ''; ?>>Music</option>
                        <option value="Toys" <?php echo $auction['category'] == 'Toys' ? 'selected' : ''; ?>>Toys</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-dollar-sign"></i> Current Price</label>
                    <input type="number" name="current_price" step="0.01" required value="<?php echo $auction['current_price']; ?>">
                    <small>Changing price affects current bidding</small>
                </div>
            </div>
            
         
            
            <!-- Read-only fields for important information -->
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Seller</label>
                    <input type="text" value="<?php echo htmlspecialchars($auction['seller_name']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-chart-line"></i> Starting Price</label>
                    <input type="text" value="$<?php echo number_format($auction['starting_price'], 2); ?>" disabled>
                </div>
            </div>
            
            <div class="form-info">
                <h4><i class="fas fa-info-circle"></i> Important Notes</h4>
                <ul>
                    <li><i class="fas fa-ban"></i> <strong>Status cannot be edited</strong> - It is automatically managed by the system</li>
                    <li><i class="fas fa-dollar-sign"></i> Changing the current price will affect ongoing bidding</li>
                    <li><i class="fas fa-history"></i> All changes are logged for security purposes</li>
                    <li><i class="fas fa-clock"></i> The auction end time cannot be changed here</li>
                    <li><i class="fas fa-user"></i> Seller information cannot be changed</li>
                </ul>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="index.php?action=admin-auctions" class="btn btn-secondary">Cancel</a>
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

.form-header p {
    color: var(--text-secondary);
    font-size: 0.9rem;
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
    transition: var(--transition);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-gold);
    box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
}

.form-group input:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: var(--text-muted);
    font-size: 11px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.status-display {
    background: var(--dark-card);
    padding: 1rem;
    border-radius: 12px;
    border: 1px solid var(--border-color);
}

.status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 8px;
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

.status-note {
    display: block;
    color: var(--text-muted);
    font-size: 11px;
    margin-top: 5px;
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
    margin-bottom: 0.8rem;
}

.form-info ul {
    margin-left: 1.2rem;
    color: var(--text-secondary);
}

.form-info ul li {
    padding: 5px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-info ul li i {
    color: var(--primary-gold);
    width: 20px;
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