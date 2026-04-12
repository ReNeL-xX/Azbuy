<?php
// Define base URL for assets (updated to Azbuy folder)
$base_url = '/AzBuy/public';
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
                <li><a href="<?php echo $base_url; ?>/index.php?action=home" class="<?php echo ($page_title ?? '') == 'Home' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?php echo $base_url; ?>/index.php?action=about" class="<?php echo ($page_title ?? '') == 'About' ? 'active' : ''; ?>"><i class="fas fa-info-circle"></i> About</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Show these when user is LOGGED IN -->
                    <li><a href="<?php echo $base_url; ?>/index.php?action=dashboard"><i class="fas fa-store"></i> Marketplace</a></li>
                    <li><a href="<?php echo $base_url; ?>/index.php?action=my-auctions"><i class="fas fa-gavel"></i> My Auctions</a></li>
                    <li><a href="<?php echo $base_url; ?>/index.php?action=my-bids"><i class="fas fa-chart-line"></i> Activities</a></li>
                    <li><a href="<?php echo $base_url; ?>/index.php?action=settings"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="javascript:void(0)" onclick="if(confirm('Are you sure you want to logout?')) window.location.href='<?php echo $base_url; ?>/index.php?action=logout'"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <!-- Show these when user is LOGGED OUT -->
                    <li><a href="<?php echo $base_url; ?>/index.php?action=login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li><a href="<?php echo $base_url; ?>/index.php?action=register"><i class="fas fa-user-plus"></i> Sign Up</a></li>
                <?php endif; ?>
            </ul>
            
            <?php if (isset($_SESSION['username'])): ?>
                <div class="user-greeting">
                    <i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
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