<?php 
$page_title = "About";
ob_start();
?>

<div class="about-container">
    <div class="about-hero">
        <h1>About Azbuy</h1>
        <p>Your Trusted Online Auction Marketplace Since 2024</p>
    </div>
    
    <div class="about-content">
        <div class="about-section">
            <h2><i class="fas fa-rocket"></i> Our Mission</h2>
            <p>At Azbuy, we're revolutionizing the way people buy and sell valuable items. Our platform provides a secure, transparent, and exciting auction experience for collectors, enthusiasts, and everyday shoppers alike. We believe in creating a marketplace where every item finds its true value through fair and competitive bidding.</p>
        </div>
        
        <div class="about-section">
            <h2><i class="fas fa-check-circle"></i> Why Choose Us?</h2>
            <div class="features-list">
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <h4>Secure Bidding System</h4>
                        <p>Real-time updates with bank-grade security</p>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="fas fa-boxes"></i>
                    <div>
                        <h4>Wide Range of Categories</h4>
                        <p>From electronics to rare collectibles</p>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="fas fa-chart-line"></i>
                    <div>
                        <h4>Transparent Process</h4>
                        <p>Clear rules and real-time bid tracking</p>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="fas fa-credit-card"></i>
                    <div>
                        <h4>Secure Payments</h4>
                        <p>Protected transactions and seller verification</p>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="fas fa-headset"></i>
                    <div>
                        <h4>24/7 Customer Support</h4>
                        <p>Always here to help you</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="about-section">
            <h2><i class="fas fa-handshake"></i> How It Works</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <i class="fas fa-user-plus step-icon"></i>
                    <h3>Create Account</h3>
                    <p>Sign up for free and start bidding immediately</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <i class="fas fa-search step-icon"></i>
                    <h3>Browse Auctions</h3>
                    <p>Discover unique items across multiple categories</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <i class="fas fa-gavel step-icon"></i>
                    <h3>Place Bids</h3>
                    <p>Bid on items you love before time runs out</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <i class="fas fa-trophy step-icon"></i>
                    <h3>Win & Pay</h3>
                    <p>Secure your item with our safe payment system</p>
                </div>
            </div>
        </div>
        
        <div class="about-section">
            <h2><i class="fas fa-envelope"></i> Contact Us</h2>
            <div class="contact-info">
                <p><i class="fas fa-envelope"></i> support@azbuy.com</p>
                <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                <p><i class="fas fa-map-marker-alt"></i> 123 Auction Street, New York, NY 10001</p>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>