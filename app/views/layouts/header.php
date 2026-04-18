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
            
            <?php if (isset($_SESSION['username'])): ?>
                <div class="user-greeting">
                    <i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                        <span class="admin-badge" style="background: var(--primary-gold); color: #000; padding: 2px 8px; border-radius: 20px; font-size: 10px; margin-left: 8px;">Admin</span>
                    <?php endif; ?>
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