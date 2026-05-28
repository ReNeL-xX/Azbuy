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
        <!-- Mission Section -->
        <div class="about-section">
            <div class="section-icon">
                <i class="fas fa-rocket"></i>
            </div>
            <h2>Our Mission</h2>
            <p>At Azbuy, we're revolutionizing the way people buy and sell valuable items. Our platform provides a secure, transparent, and exciting auction experience for collectors, enthusiasts, and everyday shoppers alike. We believe in creating a marketplace where every item finds its true value through fair and competitive bidding.</p>
        </div>
        
        <!-- Why Choose Us Section - Clean Grid -->
        <div class="about-section">
            <div class="section-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Why Choose Us?</h2>
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Secure Bidding System</h4>
                        <p>Real-time updates with bank-grade security</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Wide Range of Categories</h4>
                        <p>From electronics to rare collectibles</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Transparent Process</h4>
                        <p>Clear rules and real-time bid tracking</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Secure Payments</h4>
                        <p>Protected transactions and seller verification</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="feature-content">
                        <h4>24/7 Customer Support</h4>
                        <p>Always here to help you</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- How It Works Section - Clean Steps -->
        <div class="about-section">
            <div class="section-icon">
                <i class="fas fa-handshake"></i>
            </div>
            <h2>How It Works</h2>
            <div class="steps-container">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3>Create Account</h3>
                    <p>Sign up for free and start bidding immediately</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Browse Auctions</h3>
                    <p>Discover unique items across multiple categories</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <i class="fas fa-gavel"></i>
                    </div>
                    <h3>Place Bids</h3>
                    <p>Bid on items you love before time runs out</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3>Win & Pay</h3>
                    <p>Secure your item with our safe payment system</p>
                </div>
            </div>
        </div>
        
        <!-- Contact Section -->
        <div class="about-section contact-section">
            <div class="section-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <h2>Contact Us</h2>
            <div class="contact-grid">
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h4>Email</h4>
                        <p>support@azbuy.com</p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h4>Phone</h4>
                        <p>0945-259-4288</p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h4>Address</h4>
                        <p>Brgy. Alijis, Bacolod City, Negros Occidental</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.about-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

/* Hero Section */
.about-hero {
    text-align: center;
    padding: 3rem 2rem;
    background: linear-gradient(135deg, var(--dark-elevated) 0%, var(--dark-bg) 100%);
    border-radius: 24px;
    margin-bottom: 3rem;
    border: 1px solid var(--border-color);
}

.about-hero h1 {
    color: var(--primary-gold);
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.about-hero p {
    color: var(--text-secondary);
    font-size: 1rem;
}

/* About Sections */
.about-section {
    background: var(--dark-elevated);
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.about-section:hover {
    border-color: var(--primary-gold);
    box-shadow: 0 4px 20px rgba(255, 215, 0, 0.1);
}

.section-icon {
    text-align: center;
    margin-bottom: 1rem;
}

.section-icon i {
    font-size: 2rem;
    color: var(--primary-gold);
}

.about-section h2 {
    text-align: center;
    color: var(--primary-gold);
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
}

.about-section p {
    color: var(--text-secondary);
    line-height: 1.6;
    text-align: center;
}

/* Features Grid */
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
}

.feature-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: var(--dark-card);
    border-radius: 16px;
    transition: var(--transition);
}

.feature-item:hover {
    transform: translateX(5px);
    background: rgba(255, 215, 0, 0.05);
}

.feature-icon {
    flex-shrink: 0;
}

.feature-icon i {
    font-size: 1.5rem;
    color: var(--primary-gold);
}

.feature-content h4 {
    color: var(--text-primary);
    margin-bottom: 0.25rem;
    font-size: 1rem;
}

.feature-content p {
    color: var(--text-muted);
    font-size: 0.85rem;
    text-align: left;
}

/* Steps Container */
.steps-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 2rem;
    margin-top: 1rem;
}

.step {
    text-align: center;
    padding: 1.5rem;
    background: var(--dark-card);
    border-radius: 20px;
    transition: var(--transition);
}

.step:hover {
    transform: translateY(-5px);
    background: rgba(255, 215, 0, 0.05);
}

.step-number {
    width: 40px;
    height: 40px;
    background: var(--gradient-gold);
    color: #000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0 auto 1rem;
}

.step-icon i {
    font-size: 2rem;
    color: var(--primary-gold);
    margin-bottom: 0.75rem;
}

.step h3 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.step p {
    color: var(--text-muted);
    font-size: 0.85rem;
    text-align: center;
}

/* Contact Section */
.contact-section {
    text-align: center;
}

.contact-grid {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 2rem;
    margin-top: 1rem;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    background: var(--dark-card);
    border-radius: 16px;
    transition: var(--transition);
}

.contact-item:hover {
    transform: translateY(-3px);
    border-color: var(--primary-gold);
    background: rgba(255, 215, 0, 0.05);
}

.contact-item i {
    font-size: 1.5rem;
    color: var(--primary-gold);
}

.contact-item h4 {
    color: var(--text-primary);
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.contact-item p {
    color: var(--text-muted);
    font-size: 0.85rem;
    text-align: left;
}

/* Responsive */
@media (max-width: 768px) {
    .about-container {
        padding: 1rem;
    }
    
    .about-hero {
        padding: 2rem 1rem;
    }
    
    .about-hero h1 {
        font-size: 1.8rem;
    }
    
    .about-section {
        padding: 1.5rem;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
    
    .steps-container {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .contact-grid {
        flex-direction: column;
        align-items: stretch;
    }
    
    .contact-item {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .feature-item {
        flex-direction: column;
        text-align: center;
        align-items: center;
    }
    
    .feature-content p {
        text-align: center;
    }
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>