<?php
// Define base URL for assets
$base_url = '/AzBuy/public';

// Get current action to highlight active nav
$current_action = $_GET['action'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Azbuy - <?php echo $page_title ?? 'Premium Auction Marketplace'; ?></title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <a href="<?php echo $base_url; ?>/index.php?action=home">
                    <i class="fas fa-gavel"></i> Azbuy
                </a>
            </div>
            <ul class="nav-menu">
                <!-- Home Link -->
                <li>
                    <a href="<?php echo $base_url; ?>/index.php?action=home" 
                       class="<?php echo $current_action == 'home' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                
                <!-- About Link -->
                <li>
                    <a href="<?php echo $base_url; ?>/index.php?action=about" 
                       class="<?php echo $current_action == 'about' ? 'active' : ''; ?>">
                        <i class="fas fa-info-circle"></i> About
                    </a>
                </li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Show these when user is LOGGED IN -->
                    
                    <!-- Dashboard / Marketplace Link -->
                    <li>
                        <a href="<?php echo $base_url; ?>/index.php?action=dashboard" 
                           class="<?php echo $current_action == 'dashboard' ? 'active' : ''; ?>">
                            <i class="fas fa-store"></i> Marketplace
                        </a>
                    </li>
                    
                    <!-- My Auctions Link -->
                    <li>
                        <a href="<?php echo $base_url; ?>/index.php?action=my-auctions" 
                           class="<?php echo $current_action == 'my-auctions' ? 'active' : ''; ?>">
                            <i class="fas fa-gavel"></i> My Auctions
                        </a>
                    </li>
                    
                    <!-- Activities / My Bids Link -->
                    <li>
                        <a href="<?php echo $base_url; ?>/index.php?action=my-bids" 
                           class="<?php echo $current_action == 'my-bids' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-line"></i> Activities
                        </a>
                    </li>
                    
                    <!-- Settings Link -->
                    <li>
                        <a href="<?php echo $base_url; ?>/index.php?action=settings" 
                           class="<?php echo $current_action == 'settings' ? 'active' : ''; ?>">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </li>
                    
                    <!-- Admin Panel Link - Only visible to admins -->
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                        <li>
                            <a href="<?php echo $base_url; ?>/index.php?action=admin-dashboard" 
                               class="<?php echo strpos($current_action, 'admin') === 0 ? 'active' : ''; ?>">
                                <i class="fas fa-user-shield"></i> Admin Panel
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Logout Link -->
                    <li>
                        <a href="javascript:void(0)" 
                           onclick="if(confirm('Are you sure you want to logout?')) window.location.href='<?php echo $base_url; ?>/index.php?action=logout'">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                    
                <?php else: ?>
                    <!-- Show these when user is LOGGED OUT -->
                    
                    <!-- Login Link -->
                    <li>
                        <a href="<?php echo $base_url; ?>/index.php?action=login" 
                           class="<?php echo $current_action == 'login' ? 'active' : ''; ?>">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    
                    <!-- Register Link -->
                    <li>
                        <a href="<?php echo $base_url; ?>/index.php?action=register" 
                           class="<?php echo $current_action == 'register' ? 'active' : ''; ?>">
                            <i class="fas fa-user-plus"></i> Sign Up
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <!-- User Greeting and Notification Bell -->
       <?php if (isset($_SESSION['user_id'])): ?>
    <div class="notification-bell" style="position: relative; margin-left: 1rem;">
        <i class="fas fa-bell" onclick="toggleNotifications()" style="font-size: 1.2rem; cursor: pointer; color: var(--primary-gold);"></i>
        <span id="notificationCount" class="notification-count" style="position: absolute; top: -8px; right: -8px; background: #dc3545; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; min-width: 18px; text-align: center; display: none;">0</span>
        
        <div id="notificationDropdown" class="notification-dropdown" style="display: none; position: absolute; top: 35px; right: 0; width: 350px; background: var(--dark-elevated); border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 5px 20px rgba(0,0,0,0.3); z-index: 1000;">
            <div style="padding: 12px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: bold; color: var(--primary-gold);">Notifications</span>
                <button onclick="deleteAllNotifications()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 12px;" title="Clear all notifications">
                    <i class="fas fa-trash-alt"></i> Clear All
                </button>
            </div>
            <div id="notificationList" style="max-height: 400px; overflow-y: auto;">
                <div style="padding: 12px; text-align: center; color: var(--text-muted);">Loading...</div>
            </div>
        </div>
    </div>
<?php endif; ?>
        </div>
    </nav>
    <main>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>