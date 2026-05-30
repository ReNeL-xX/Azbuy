<?php
// Force correct base URL for Hostinger
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . '://' . $host . '/public';

// Get current action to highlight active nav
$current_action = $_GET['action'] ?? 'home';

// Get user profile picture if logged in
$profile_pic = null;
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../../config/Database.php';
    require_once __DIR__ . '/../../../app/models/User.php';
    $db = new Database();
    $conn = $db->connect();
    $userModel = new User($conn);
    $profile_pic = $userModel->getProfilePicture($_SESSION['user_id']);
    $conn->close();
}
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
    <style>
        /* Icon styles with hover effects */
        .user-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .settings-icon, .logout-icon {
            position: relative;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary) !important;
        }
        
        .settings-icon i, .logout-icon i {
            font-size: 1.2rem;
            transition: all 0.3s ease;
            color: var(--text-secondary) !important;
        }
        
        .settings-icon:hover i, .logout-icon:hover i {
            color: var(--primary-gold) !important;
            transform: translateY(-2px);
        }
        
        .settings-icon.active i {
            color: var(--primary-gold) !important;
        }
        
        .profile-avatar-header {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FFD700 0%, #DAA520 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
        }
        
        .profile-avatar-header:hover {
            transform: translateY(-2px);
            border-color: var(--primary-gold);
        }
        
        .profile-avatar-header i {
            font-size: 1.2rem;
            color: #000;
        }
        
        .profile-avatar-header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .notification-bell {
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--text-secondary) !important;
        }
        
        .notification-bell i {
            font-size: 1.2rem;
            transition: all 0.3s ease;
            color: var(--text-secondary) !important;
        }
        
        .notification-bell:hover i {
            color: var(--primary-gold) !important;
            transform: translateY(-2px);
        }
        
        .notification-bell.active i {
            color: var(--primary-gold) !important;
        }
        
        .settings-icon::after, .logout-icon::after, .notification-bell::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #FFD700 0%, #DAA520 100%);
            transition: width 0.3s ease;
        }
        
        .settings-icon:hover::after, .logout-icon:hover::after, .notification-bell:hover::after {
            width: 80%;
        }
        
        .settings-icon.active::after, .logout-icon.active::after, .notification-bell.active::after {
            width: 80%;
        }
        
        .notification-dropdown {
            display: none;
            position: absolute;
            top: 35px;
            right: 0;
            width: 350px;
            background: var(--dark-elevated);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            z-index: 1000;
        }
        
        .notification-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            min-width: 18px;
            text-align: center;
            display: none;
        }
    </style>
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
                <li>
                    <a href="<?php echo $base_url; ?>/index.php?action=home" 
                       class="<?php echo $current_action == 'home' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                
                <li>
                    <a href="<?php echo $base_url; ?>/index.php?action=about" 
                       class="<?php echo $current_action == 'about' ? 'active' : ''; ?>">
                        <i class="fas fa-info-circle"></i> About
                    </a>
                </li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li>
                        <a href="<?php echo $base_url; ?>/index.php?action=dashboard" 
                           class="<?php echo $current_action == 'dashboard' ? 'active' : ''; ?>">
                            <i class="fas fa-store"></i> Marketplace
                        </a>
                    </li>
                    
                    <li>
                        <a href="<?php echo $base_url; ?>/index.php?action=my-auctions" 
                           class="<?php echo $current_action == 'my-auctions' ? 'active' : ''; ?>">
                            <i class="fas fa-gavel"></i> My Auctions
                        </a>
                    </li>
                    
                    <li>
                        <a href="<?php echo $base_url; ?>/index.php?action=my-bids" 
                           class="<?php echo $current_action == 'my-bids' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-line"></i> Activities
                        </a>
                    </li>
                    
                    <li>
                        <a href="<?php echo $base_url; ?>/index.php?action=wallet" 
                           class="<?php echo $current_action == 'wallet' ? 'active' : ''; ?>">
                            <i class="fas fa-wallet"></i> Wallet
                        </a>
                    </li>
                    
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                        <li>
                            <a href="<?php echo $base_url; ?>/index.php?action=admin-dashboard" 
                               class="<?php echo strpos($current_action, 'admin') === 0 ? 'active' : ''; ?>">
                                <i class="fas fa-user-shield"></i> Admin Panel
                            </a>
                        </li>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <li>
                        <a href="<?php echo $base_url; ?>/index.php?action=login" 
                           class="<?php echo $current_action == 'login' ? 'active' : ''; ?>">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    
                    <li>
                        <a href="<?php echo $base_url; ?>/index.php?action=register" 
                           class="<?php echo $current_action == 'register' ? 'active' : ''; ?>">
                            <i class="fas fa-user-plus"></i> Sign Up
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-actions">
                    <a href="<?php echo $base_url; ?>/index.php?action=settings" class="profile-avatar-header" title="Profile">
    <?php 
    $header_avatar_url = '';
    if (!empty($profile_pic) && $profile_pic != 'default.jpg') {
        $clean_pic = ltrim($profile_pic, '/');
        $header_avatar_url = 'https://azbuy.bsit2a.com/' . $clean_pic;
    }
    ?>
    <?php if ($header_avatar_url): ?>
        <img src="<?php echo $header_avatar_url; ?>" alt="Profile" onerror="this.src='https://placehold.co/35x35/1a1a1a/gold?text=?'">
    <?php else: ?>
        <i class="fas fa-user-circle"></i>
    <?php endif; ?>
</a>
                    
                    <a href="<?php echo $base_url; ?>/index.php?action=settings" class="settings-icon <?php echo $current_action == 'settings' ? 'active' : ''; ?>" title="Settings">
                        <i class="fas fa-cog"></i>
                    </a>
                    
                    <a href="javascript:void(0)" onclick="if(confirm('Are you sure you want to logout?')) window.location.href='<?php echo $base_url; ?>/index.php?action=logout'" class="logout-icon" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                    
                    <div class="notification-bell" onclick="toggleNotifications()">
                        <i class="fas fa-bell"></i>
                        <span id="notificationCount" class="notification-count">0</span>
                        
                        <div id="notificationDropdown" class="notification-dropdown">
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