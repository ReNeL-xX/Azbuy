<?php 
$page_title = $profile['username'] . "'s Profile";

// Make sure $base_url is available from header.php
global $base_url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AzBuy - <?php echo htmlspecialchars($profile['username']); ?>'s Profile</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .profile-header {
            background: var(--dark-elevated);
            border-radius: 24px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }
        
        .profile-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FFD700 0%, #DAA520 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .profile-avatar-large i {
            font-size: 4rem;
            color: #000;
        }
        
        .profile-avatar-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-info {
            flex: 1;
        }
        
        .profile-info h1 {
            color: var(--primary-gold);
            margin-bottom: 0.5rem;
        }
        
        .profile-info .username {
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }
        
        .profile-info .bio {
            color: var(--text-secondary);
            margin-top: 0.5rem;
            line-height: 1.6;
        }
        
        .info-card {
            background: var(--dark-elevated);
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
        }
        
        .info-card h3 {
            color: var(--primary-gold);
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-item i {
            width: 30px;
            color: var(--primary-gold);
            font-size: 1.1rem;
        }
        
        .info-item .label {
            color: var(--text-muted);
            width: 100px;
        }
        
        .info-item .value {
            color: var(--text-primary);
            flex: 1;
        }
        
        .btn-back {
            margin-top: 1rem;
        }
        
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: transparent;
            color: var(--primary-gold);
            border: 1px solid var(--primary-gold);
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 215, 0, 0.1);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .profile-container {
                padding: 1rem;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .info-item {
                flex-direction: column;
                text-align: center;
                gap: 5px;
            }
            
            .info-item i {
                margin: 0 auto;
            }
            
            .info-item .label {
                width: auto;
            }
        }
    </style>
</head>
<body>
    <?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
    
    <main>
        <div class="profile-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar-large">
    <?php 
    $profile_avatar_url = '';
    if (!empty($profile['profile_pic']) && $profile['profile_pic'] != 'default.jpg') {
        $clean_pic = ltrim($profile['profile_pic'], '/');
        $profile_avatar_url = 'https://azbuy.bsit2a.com/' . $clean_pic;
    }
    ?>
    <?php if ($profile_avatar_url): ?>
        <img src="<?php echo $profile_avatar_url; ?>" alt="<?php echo htmlspecialchars($profile['username']); ?>" onerror="this.src='https://placehold.co/120x120/1a1a1a/gold?text=?'">
    <?php else: ?>
        <i class="fas fa-user-circle"></i>
    <?php endif; ?>
</div>
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($profile['full_name'] ?? $profile['username']); ?></h1>
                    <div class="username"><?php echo htmlspecialchars($profile['username']); ?></div>
                    <div class="bio"><?php echo nl2br(htmlspecialchars($profile['bio'] ?? 'No bio yet.')); ?></div>
                    <div class="member-since" style="margin-top: 0.5rem;">
                        <i class="fas fa-calendar"></i> Member since <?php echo date('F Y', strtotime($profile['created_at'])); ?>
                    </div>
                </div>
            </div>
            
            <!-- Seller Information Card -->
            <div class="info-card">
                <h3><i class="fas fa-store"></i> About the Seller</h3>
                <div class="info-item">
                    <i class="fas fa-user"></i>
                    <span class="label">Username:</span>
                    <span class="value"><?php echo htmlspecialchars($profile['username']); ?></span>
                </div>
                <?php if (!empty($profile['full_name'])): ?>
                <div class="info-item">
                    <i class="fas fa-id-card"></i>
                    <span class="label">Full Name:</span>
                    <span class="value"><?php echo htmlspecialchars($profile['full_name']); ?></span>
                </div>
                <?php endif; ?>
                <div class="info-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="label">Member Since:</span>
                    <span class="value"><?php echo date('F Y', strtotime($profile['created_at'])); ?></span>
                </div>
            </div>
            
            <!-- Contact Information -->
            <?php if (!empty($profile['phone']) || !empty($profile['email'])): ?>
            <div class="info-card">
                <h3><i class="fas fa-address-card"></i> Contact Information</h3>
                <?php if (!empty($profile['phone'])): ?>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <span class="label">Phone:</span>
                    <span class="value"><?php echo htmlspecialchars($profile['phone']); ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($profile['email'])): ?>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <span class="label">Email:</span>
                    <span class="value"><?php echo htmlspecialchars($profile['email']); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div class="btn-back">
                <a href="javascript:history.back()" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Go Back
                </a>
            </div>
        </div>
    </main>
    
    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</body>
</html>