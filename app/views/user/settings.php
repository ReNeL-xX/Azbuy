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
$conn->close();

ob_start();
?>

<div class="settings-container">
    <h1><i class="fas fa-cog"></i> Account Settings</h1>
    
    <div class="settings-grid">
        <div class="settings-sidebar">
            <div class="profile-summary">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                    <button class="change-avatar" onclick="document.getElementById('avatarUpload').click();">
                        <i class="fas fa-camera"></i>
                    </button>
                    <input type="file" id="avatarUpload" style="display: none;" accept="image/*">
                </div>
                <h3 id="displayFullName"><?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?></h3>
                <p id="displayUsername">@<?php echo htmlspecialchars($user['username']); ?></p>
                <div class="member-since">
                    <i class="fas fa-calendar"></i> Member since <?php echo date('F Y', strtotime($user['created_at'])); ?>
                </div>
                <div class="wallet-info" style="margin-top: 1rem; padding: 0.5rem; background: rgba(255,215,0,0.1); border-radius: 8px;">
                    <i class="fas fa-wallet"></i> Balance: $<?php echo number_format($user['balance'] ?? 0, 2); ?>
                </div>
            </div>
            
            <div class="settings-nav">
                <a href="#" class="active" data-tab="profile"><i class="fas fa-user"></i> Profile Information</a>
                <a href="#" data-tab="security"><i class="fas fa-shield-alt"></i> Security</a>
                <a href="#" data-tab="wallet"><i class="fas fa-wallet"></i> Wallet & Payments</a>
                <a href="#" data-tab="notifications"><i class="fas fa-bell"></i> Notifications</a>
                <a href="#" data-tab="preferences"><i class="fas fa-sliders-h"></i> Preferences</a>
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
                        <li>Add a profile picture to build trust with other bidders</li>
                        <li>Complete your bio to showcase your interests</li>
                        <li>Verified email addresses get a trust badge</li>
                        <li>Your username cannot be changed after 30 days</li>
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
                
                <div class="security-section" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <h3>Two-Factor Authentication (2FA)</h3>
                    <p>Add an extra layer of security to your account</p>
                    <button class="btn btn-secondary" onclick="enable2FA()">Enable 2FA</button>
                </div>
                
                <div class="security-section" style="margin-top: 2rem;">
                    <h3>Active Sessions</h3>
                    <div class="session-item" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--dark-card); border-radius: 12px; margin-bottom: 0.5rem;">
                        <div>
                            <i class="fas fa-laptop" style="color: var(--primary-gold);"></i>
                            <strong> Chrome on Windows</strong>
                            <div><small>New York, US - Active now</small></div>
                        </div>
                        <span class="current-badge" style="background: var(--primary-gold); color: #000; padding: 4px 12px; border-radius: 50px; font-size: 12px;">Current</span>
                    </div>
                    <div class="session-item" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--dark-card); border-radius: 12px;">
                        <div>
                            <i class="fas fa-mobile-alt" style="color: var(--primary-gold);"></i>
                            <strong> Safari on iPhone</strong>
                            <div><small>Last active 2 days ago</small></div>
                        </div>
                        <button class="btn-sm btn-danger" onclick="revokeSession(this)">Revoke</button>
                    </div>
                </div>
            </div>
            
            <!-- Wallet & Payments Tab -->
            <div id="wallet" class="settings-tab">
                <h2><i class="fas fa-wallet"></i> Wallet & Payment Methods</h2>
                
                <div class="wallet-balance" style="text-align: center; padding: 2rem; background: var(--dark-card); border-radius: 20px; margin-bottom: 2rem;">
                    <span>Available Balance</span>
                    <div class="balance-amount" style="font-size: 2.5rem; font-weight: 800; color: var(--primary-gold);">$<?php echo number_format($user['balance'] ?? 0, 2); ?></div>
                    <button class="btn btn-primary" onclick="addFunds()">Add Funds</button>
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
                    
                    <div class="transaction-item" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid var(--border-color);">
                        <div>
                            <strong>Welcome Bonus</strong>
                            <div><small>Initial deposit</small></div>
                        </div>
                        <span class="amount positive" style="color: #28A745;">+$100.00</span>
                        <span class="date" style="color: var(--text-muted);"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Notifications Tab -->
            <div id="notifications" class="settings-tab">
                <h2><i class="fas fa-bell"></i> Notification Preferences</h2>
                
                <div class="notification-setting" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <strong>Email Notifications</strong>
                        <p style="color: var(--text-muted); font-size: 0.85rem;">Receive updates about your bids and auctions</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked onchange="toggleNotification(this, 'email')">
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="notification-setting" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <strong>Push Notifications</strong>
                        <p style="color: var(--text-muted); font-size: 0.85rem;">Get real-time alerts when you're outbid</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked onchange="toggleNotification(this, 'push')">
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="notification-setting" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <strong>Auction Ending Alerts</strong>
                        <p style="color: var(--text-muted); font-size: 0.85rem;">Get notified 1 hour before auctions end</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked onchange="toggleNotification(this, 'auction_end')">
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="notification-setting" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <strong>Marketing Emails</strong>
                        <p style="color: var(--text-muted); font-size: 0.85rem;">Receive special offers and new arrivals</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" onchange="toggleNotification(this, 'marketing')">
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="notification-setting" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0;">
                    <div>
                        <strong>SMS Alerts</strong>
                        <p style="color: var(--text-muted); font-size: 0.85rem;">Get text messages for important updates</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" onchange="toggleNotification(this, 'sms')">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
            
            <!-- Preferences Tab -->
            <div id="preferences" class="settings-tab">
                <h2><i class="fas fa-sliders-h"></i> Preferences</h2>
                
                <form id="preferencesForm" onsubmit="savePreferences(event)">
                    <div class="form-group">
                        <label><i class="fas fa-dollar-sign"></i> Currency Display</label>
                        <select id="currency" onchange="updateCurrency()">
                            <option value="USD">USD ($) - US Dollar</option>
                            <option value="EUR">EUR (€) - Euro</option>
                            <option value="GBP">GBP (£) - British Pound</option>
                            <option value="JPY">JPY (¥) - Japanese Yen</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Time Zone</label>
                        <select id="timezone">
                            <option value="EST">Eastern Time (ET)</option>
                            <option value="CST">Central Time (CT)</option>
                            <option value="MST">Mountain Time (MT)</option>
                            <option value="PST">Pacific Time (PT)</option>
                            <option value="GMT">Greenwich Mean Time (GMT)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-language"></i> Language</label>
                        <select id="language">
                            <option value="en">English</option>
                            <option value="es">Español</option>
                            <option value="fr">Français</option>
                            <option value="de">Deutsch</option>
                            <option value="ja">日本語</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-chart-line"></i> Bid Increment Type</label>
                        <select id="bidIncrement">
                            <option value="auto">Automatic (Recommended)</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-eye"></i> Outbid Notifications</label>
                        <select id="outbidNotify">
                            <option value="immediate">Immediately</option>
                            <option value="daily">Daily Digest</option>
                            <option value="never">Never</option>
                        </select>
                    </div>
                    
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">Save Preferences</button>
                        <button type="button" class="btn btn-secondary" onclick="resetPreferences()">Reset to Default</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast Notification -->
<div id="successToast" style="display: none; position: fixed; bottom: 20px; right: 20px; background: var(--primary-gold); color: #000; padding: 12px 24px; border-radius: 50px; z-index: 9999; animation: slideInRight 0.3s ease;">
    <i class="fas fa-check-circle"></i> <span id="toastMessage">Changes saved!</span>
</div>

<style>
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

.current-badge {
    background: var(--primary-gold);
    color: #000;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 12px;
}

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

.settings-tab {
    display: none;
}

.settings-tab.active {
    display: block;
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

// Avatar Upload
document.getElementById('avatarUpload')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const avatarIcon = document.querySelector('.profile-avatar i');
            avatarIcon.style.background = `url(${e.target.result}) center/cover`;
            avatarIcon.style.color = 'transparent';
            showToast('Profile picture updated!');
        };
        reader.readAsDataURL(file);
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

function enable2FA() {
    showToast('2FA setup would begin here. Check your email for verification code.');
}

function revokeSession(button) {
    button.closest('.session-item').remove();
    showToast('Session revoked successfully');
}

// Wallet Functions
function addFunds() {
    const amount = prompt('Enter amount to add:', '100');
    if (amount && !isNaN(amount) && amount > 0) {
        showToast(`$${amount} added to your wallet! (Demo mode)`);
    }
}

function addPaymentMethod() {
    showToast('Payment method form would open here');
}

function removePaymentMethod(button) {
    if (confirm('Remove this payment method?')) {
        button.closest('.payment-card').remove();
        showToast('Payment method removed');
    }
}

// Notification Functions
function toggleNotification(checkbox, type) {
    const status = checkbox.checked ? 'enabled' : 'disabled';
    showToast(`${type} notifications ${status}`);
}

// Preferences Functions
function savePreferences(event) {
    event.preventDefault();
    showToast('Preferences saved successfully!');
    return false;
}

function resetPreferences() {
    document.getElementById('currency').value = 'USD';
    document.getElementById('timezone').value = 'EST';
    document.getElementById('language').value = 'en';
    document.getElementById('bidIncrement').value = 'auto';
    document.getElementById('outbidNotify').value = 'immediate';
    showToast('Preferences reset to default');
}

function updateCurrency() {
    const currency = document.getElementById('currency').value;
    const symbol = currency === 'USD' ? '$' : currency === 'EUR' ? '€' : currency === 'GBP' ? '£' : '¥';
    showToast(`Currency changed to ${currency} (${symbol})`);
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
?>