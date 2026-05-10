<?php 
$page_title = "Settings";

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

// Get user data from database
require_once dirname(__DIR__) . '/../models/User.php';
require_once dirname(__DIR__) . '/../../config/Database.php';

$database = new Database();
$conn = $database->connect();
$userModel = new User($conn);
$user = $userModel->getUserById($_SESSION['user_id']);
$transactions = $userModel->getTransactions($_SESSION['user_id'], 10);
$profile_pic = $userModel->getProfilePicture($_SESSION['user_id']);

ob_start();
?>

<div class="settings-container">
    <h1><i class="fas fa-cog"></i> Account Settings</h1>
    
    <div class="settings-grid">
        <div class="settings-sidebar">
            <div class="profile-summary">
                <div class="profile-avatar-wrapper">
                    <div class="profile-avatar" id="profileAvatar">
                        <?php if ($profile_pic): ?>
                            <img src="/AzBuy/public/<?php echo $profile_pic; ?>" alt="Profile Picture">
                        <?php else: ?>
                            <i class="fas fa-user-circle"></i>
                        <?php endif; ?>
                    </div>
                    
                    <form action="index.php?action=upload-profile-pic" method="POST" enctype="multipart/form-data" id="avatarForm">
                        <input type="file" name="profile_pic" id="avatarUpload" accept="image/*" style="display: none;">
                        <label for="avatarUpload" class="change-avatar-btn">
                            <i class="fas fa-camera"></i>
                            <span>Change Photo</span>
                        </label>
                    </form>
                </div>
                <h3 id="displayFullName"><?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?></h3>
                <p id="displayUsername">@<?php echo htmlspecialchars($user['username']); ?></p>
                <div class="member-since">
                    <i class="fas fa-calendar"></i> Member since <?php echo date('F Y', strtotime($user['created_at'])); ?>
                </div>
                <div class="wallet-info">
                    <i class="fas fa-wallet"></i> Balance: ₱<?php echo number_format($user['balance'] ?? 0, 2); ?>
                </div>
            </div>
            
            <div class="settings-nav">
                <a href="#" class="active" data-tab="profile"><i class="fas fa-user"></i> Profile Information</a>
                <a href="#" data-tab="security"><i class="fas fa-shield-alt"></i> Security</a>
                <a href="#" data-tab="wallet"><i class="fas fa-wallet"></i> Wallet & Payments</a>
            </div>
        </div>
        
        <div class="settings-content">
            <!-- Profile Information Tab -->
            <div id="profile" class="settings-tab active">
                <h2><i class="fas fa-user-edit"></i> Profile Information</h2>
                
                <form action="index.php?action=update-profile" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Full Name</label>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" placeholder="Your full name">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-at"></i> Username</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                            <small>Username cannot be changed</small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            <small>Email cannot be changed</small>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Phone Number</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="Phone number">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Address</label>
                        <textarea name="address" rows="3" placeholder="Your address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-info-circle"></i> Bio</label>
                        <textarea id="bio" name="bio" rows="2" placeholder="Tell other bidders about yourself..."><?php echo htmlspecialchars($user['bio'] ?? 'Avid collector and vintage enthusiast. Looking for unique pieces from around the world! ✨'); ?></textarea>
                    </div>
                    
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" onclick="resetProfile()">Reset</button>
                    </div>
                </form>
                
                <div class="form-info" style="margin-top: 2rem;">
                    <h4><i class="fas fa-info-circle"></i> Profile Tips</h4>
                    <ul>
                        <li><i class="fas fa-camera"></i> Add a profile picture to build trust with other bidders</li>
                        <li><i class="fas fa-file-alt"></i> Complete your bio to showcase your interests</li>
                        <li><i class="fas fa-check-circle"></i> Verified email addresses get a trust badge</li>
                        <li><i class="fas fa-info-circle"></i> Your username cannot be changed after 30 days</li>
                    </ul>
                </div>
            </div>
            
            <!-- Security Tab -->
            <div id="security" class="settings-tab">
                <h2><i class="fas fa-shield-alt"></i> Security Settings</h2>
                
                <form action="index.php?action=update-password" method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Current Password</label>
                        <input type="password" name="current_password" required placeholder="Enter current password">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-key"></i> New Password</label>
                            <input type="password" name="new_password" id="newPassword" required placeholder="Enter new password">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-check-circle"></i> Confirm New Password</label>
                            <input type="password" name="confirm_password" id="confirmPassword" required placeholder="Confirm new password">
                        </div>
                    </div>
                    
                    <div class="password-strength" id="passwordStrength" style="margin-bottom: 1rem;">
                        <small>Password strength: <span id="strengthText">Not set</span></small>
                        <div class="strength-bar">
                            <div class="strength-progress" style="width: 0%;"></div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </form>
            </div>
            
            <!-- Wallet & Payments Tab -->
            <div id="wallet" class="settings-tab">
                <h2><i class="fas fa-wallet"></i> Wallet & Payment Methods</h2>
                
                <div class="wallet-balance" style="text-align: center; padding: 2rem; background: var(--dark-card); border-radius: 20px; margin-bottom: 2rem;">
                    <span>Available Balance</span>
                    <div class="balance-amount" style="font-size: 2.5rem; font-weight: 800; color: var(--primary-gold);">₱<?php echo number_format($user['balance'] ?? 0, 2); ?></div>
                    <a href="index.php?action=wallet" class="btn btn-primary">
                        <i class="fas fa-wallet"></i> View Full Wallet
                    </a>
                </div>
                
                <div class="payment-methods-section">
                    <h3>Saved Payment Methods</h3>
                    
                    <div class="payment-card" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: var(--dark-card); border-radius: 12px; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fab fa-cc-visa" style="font-size: 2rem; color: var(--primary-gold);"></i>
                            <div>
                                <strong>Visa ending in 4242</strong>
                                <div><small>Expires 12/2026</small></div>
                            </div>
                        </div>
                        <button class="btn-sm btn-danger" onclick="removePaymentMethod(this)">Remove</button>
                    </div>
                    
                    <div class="payment-card" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: var(--dark-card); border-radius: 12px; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fab fa-paypal" style="font-size: 2rem; color: var(--primary-gold);"></i>
                            <div>
                                <strong>PayPal</strong>
                                <div><small><?php echo htmlspecialchars($user['email']); ?></small></div>
                            </div>
                        </div>
                        <button class="btn-sm btn-danger" onclick="removePaymentMethod(this)">Remove</button>
                    </div>
                    
                    <button class="btn btn-secondary" onclick="addPaymentMethod()">+ Add Payment Method</button>
                </div>
                
                <div class="transaction-history" style="margin-top: 2rem;">
                    <h3>Recent Transactions</h3>
                    
                    <?php if (empty($transactions)): ?>
                        <div class="empty-transactions">
                            <i class="fas fa-receipt"></i>
                            <p>No transactions yet</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table class="transactions-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($transaction['created_at'])); ?></td>
                                            <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                                            <td>
                                                <?php if ($transaction['type'] == 'credit'): ?>
                                                    <span class="credit-badge">+ Credit</span>
                                                <?php else: ?>
                                                    <span class="debit-badge">- Debit</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="<?php echo $transaction['type']; ?>-amount">
                                                <?php echo $transaction['type'] == 'credit' ? '+' : '-'; ?>₱<?php echo number_format($transaction['amount'], 2); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div style="text-align: center; margin-top: 1rem;">
                            <a href="index.php?action=wallet" class="btn btn-secondary btn-sm">View All Transactions →</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast Notification -->
<div id="successToast" style="display: none; position: fixed; bottom: 20px; right: 20px; background: var(--primary-gold); color: #000; padding: 12px 24px; border-radius: 50px; z-index: 9999; animation: slideInRight 0.3s ease;">
    <i class="fas fa-check-circle"></i> <span id="toastMessage">Changes saved!</span>
</div>

<style>
/* Settings Container */
.settings-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.settings-container h1 {
    color: var(--primary-gold);
    margin-bottom: 2rem;
}

/* Settings Grid */
.settings-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
}

/* Sidebar */
.settings-sidebar {
    background: var(--dark-elevated);
    border-radius: 20px;
    padding: 1.5rem;
    border: 1px solid var(--border-color);
    position: sticky;
    top: 100px;
    height: fit-content;
}

.profile-summary {
    text-align: center;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.profile-avatar-wrapper {
    text-align: center;
    margin-bottom: 1rem;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    margin: 0 auto;
    background: linear-gradient(135deg, #FFD700 0%, #DAA520 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    overflow: hidden;
}

.profile-avatar i {
    font-size: 4rem;
    color: #000;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

#avatarForm {
    text-align: center;
    margin-top: 10px;
}

.change-avatar-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #FFD700 0%, #DAA520 100%);
    color: #000;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.change-avatar-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.change-avatar-btn i {
    font-size: 12px;
}

.member-since {
    margin-top: 0.5rem;
    font-size: 12px;
    color: var(--text-muted);
}

.wallet-info {
    margin-top: 1rem;
    padding: 0.5rem;
    background: rgba(255,215,0,0.1);
    border-radius: 8px;
}

.wallet-info i {
    color: var(--primary-gold);
    margin-right: 8px;
}

/* Settings Navigation */
.settings-nav {
    margin-top: 1.5rem;
}

.settings-nav a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    color: var(--text-secondary);
    text-decoration: none;
    border-radius: 12px;
    transition: var(--transition);
    margin-bottom: 0.5rem;
}

.settings-nav a:hover,
.settings-nav a.active {
    background: rgba(255, 215, 0, 0.1);
    color: var(--primary-gold);
}

/* Content Area */
.settings-content {
    background: var(--dark-elevated);
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid var(--border-color);
}

.settings-tab {
    display: none;
}

.settings-tab.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

.settings-tab h2 {
    color: var(--primary-gold);
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
}

/* Form Elements */
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
    font-weight: 500;
    font-size: 0.9rem;
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

.button-group {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

/* Buttons */
.btn-primary, .btn-secondary, .btn-danger {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    border: none;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, #FFD700 0%, #DAA520 100%);
    color: #000;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.btn-secondary {
    background: transparent;
    color: var(--primary-gold);
    border: 1px solid var(--primary-gold);
}

.btn-secondary:hover {
    background: rgba(255, 215, 0, 0.1);
    transform: translateY(-2px);
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
    transform: translateY(-2px);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

/* Form Info */
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
    list-style: none;
    padding-left: 0;
}

.form-info ul li {
    padding: 5px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-info ul li i {
    color: var(--primary-gold);
    width: 25px;
}

/* Password Strength */
.password-strength {
    margin-top: 0.5rem;
}

.strength-bar {
    height: 4px;
    background: var(--dark-card);
    border-radius: 4px;
    margin-top: 5px;
    overflow: hidden;
}

.strength-progress {
    height: 100%;
    width: 0%;
    transition: width 0.3s ease;
    border-radius: 4px;
}

/* Wallet Balance */
.wallet-balance {
    text-align: center;
    padding: 2rem;
    background: var(--dark-card);
    border-radius: 20px;
    margin-bottom: 2rem;
}

.balance-amount {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--primary-gold);
    margin: 0.5rem 0;
}

/* Payment Card */
.payment-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: var(--dark-card);
    border-radius: 12px;
    margin-bottom: 1rem;
    border: 1px solid var(--border-color);
}

.payment-card i {
    font-size: 2rem;
    color: var(--primary-gold);
}

/* Transactions Table */
.transactions-table {
    width: 100%;
    border-collapse: collapse;
}

.transactions-table th {
    background: rgba(255, 215, 0, 0.1);
    color: var(--primary-gold);
    padding: 12px;
    text-align: left;
    font-weight: 600;
}

.transactions-table td {
    padding: 12px;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-secondary);
}

.credit-badge {
    background: rgba(40, 167, 69, 0.15);
    color: #28a745;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 11px;
}

.debit-badge {
    background: rgba(220, 53, 69, 0.15);
    color: #dc3545;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 11px;
}

.credit-amount {
    color: #28a745;
    font-weight: bold;
}

.debit-amount {
    color: #dc3545;
    font-weight: bold;
}

.empty-transactions {
    text-align: center;
    padding: 2rem;
    color: var(--text-muted);
}

.empty-transactions i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

/* Toast Animation */
@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Responsive */
@media (max-width: 1024px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
    
    .settings-sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .settings-container {
        padding: 1rem;
    }
    
    .settings-content {
        padding: 1.5rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .button-group {
        flex-direction: column;
    }
    
    .button-group .btn {
        justify-content: center;
    }
    
    .payment-card {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .change-avatar-btn span {
        display: none;
    }
    
    .change-avatar-btn {
        padding: 6px 10px;
    }
    
    .profile-avatar {
        width: 100px;
        height: 100px;
    }
    
    .profile-avatar i {
        font-size: 3rem;
    }
}

@media (max-width: 480px) {
    .wallet-balance .balance-amount {
        font-size: 1.8rem;
    }
    
    .transactions-table th,
    .transactions-table td {
        padding: 8px;
        font-size: 12px;
    }
}
</style>

<script>
// Profile Functions
function resetProfile() {
    document.querySelector('input[name="full_name"]').value = '';
    document.querySelector('input[name="phone"]').value = '';
    document.querySelector('textarea[name="address"]').value = '';
    document.getElementById('bio').value = 'Avid collector and vintage enthusiast. Looking for unique pieces from around the world! ✨';
    showToast('Profile reset to default');
}

// Avatar Upload - Auto submit when file selected
document.getElementById('avatarUpload')?.addEventListener('change', function(e) {
    if (this.files.length > 0) {
        const file = this.files[0];
        
        // Check file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            showToast('Image size must be less than 2MB', 'error');
            this.value = '';
            return;
        }
        
        // Check file type
        if (!file.type.match('image.*')) {
            showToast('Please select an image file', 'error');
            this.value = '';
            return;
        }
        
        // Show preview before upload
        const reader = new FileReader();
        reader.onload = function(e) {
            const profileAvatar = document.querySelector('.profile-avatar');
            profileAvatar.innerHTML = `<img src="${e.target.result}" alt="Profile Picture">`;
        };
        reader.readAsDataURL(file);
        
        // Submit the form
        document.getElementById('avatarForm').submit();
    }
});

// Password strength checker
document.getElementById('newPassword')?.addEventListener('input', function() {
    const password = this.value;
    const strengthText = document.getElementById('strengthText');
    const strengthBar = document.querySelector('.strength-progress');
    
    let strength = 0;
    let message = '';
    
    if (password.length >= 8) strength += 25;
    if (password.match(/[a-z]/)) strength += 25;
    if (password.match(/[A-Z]/)) strength += 25;
    if (password.match(/[0-9]/)) strength += 25;
    if (password.match(/[^a-zA-Z0-9]/)) strength += 25;
    
    if (strength <= 25) message = 'Weak';
    else if (strength <= 50) message = 'Fair';
    else if (strength <= 75) message = 'Good';
    else message = 'Strong';
    
    strengthText.innerText = message;
    strengthBar.style.width = Math.min(strength, 100) + '%';
    
    if (strength <= 25) strengthBar.style.background = '#DC3545';
    else if (strength <= 50) strengthBar.style.background = '#FFC107';
    else if (strength <= 75) strengthBar.style.background = '#17A2B8';
    else strengthBar.style.background = '#28A745';
});

// Wallet Functions
function addPaymentMethod() {
    showToast('Payment method form would open here');
}

function removePaymentMethod(button) {
    if (confirm('Remove this payment method?')) {
        button.closest('.payment-card').remove();
        showToast('Payment method removed');
    }
}

// Toast Notification
function showToast(message, type = 'success') {
    const toast = document.getElementById('successToast');
    const toastMessage = document.getElementById('toastMessage');
    toastMessage.innerText = message;
    
    if (type === 'error') {
        toast.style.background = '#DC3545';
        toast.style.color = 'white';
    } else {
        toast.style.background = 'var(--primary-gold)';
        toast.style.color = '#000';
    }
    
    toast.style.display = 'flex';
    toast.style.alignItems = 'center';
    toast.style.gap = '8px';
    
    setTimeout(() => {
        toast.style.display = 'none';
    }, 3000);
}

// Settings Tabs
document.querySelectorAll('.settings-nav a').forEach(tab => {
    tab.addEventListener('click', (e) => {
        e.preventDefault();
        const tabId = tab.dataset.tab;
        
        document.querySelectorAll('.settings-nav a').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        document.querySelectorAll('.settings-tab').forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById(tabId).classList.add('active');
    });
});
</script>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';

// Close the connection at the very end
$conn->close();
?>