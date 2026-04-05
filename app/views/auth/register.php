<?php 
$page_title = "Sign Up";
ob_start();
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-user-plus"></i>
            <h2>Create Your Azbuy Account</h2>
            <p>Join the premier auction marketplace today</p>
        </div>
        
        <form action="index.php?action=register-process" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username *</label>
                    <input type="text" name="username" required placeholder="Choose a username">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email *</label>
                    <input type="email" name="email" required placeholder="Enter your email">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user-circle"></i> Full Name</label>
                    <input type="text" name="full_name" placeholder="Your full name">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="tel" name="phone" placeholder="Your phone number">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password *</label>
                    <input type="password" name="password" id="password" required placeholder="Create a password">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-check-circle"></i> Confirm Password *</label>
                    <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm your password">
                </div>
            </div>
            
            <div class="form-checkbox">
                <input type="checkbox" id="terms" required>
                <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Create Account</button>
        </form>
        
        <div class="auth-divider">
            <span>or</span>
        </div>
        
        <div class="social-login">
            <button class="btn-social google" type="button"><i class="fab fa-google"></i> Sign up with Google</button>
            <button class="btn-social facebook" type="button"><i class="fab fa-facebook-f"></i> Sign up with Facebook</button>
        </div>
        
        <p class="auth-link">Already have an account? <a href="index.php?action=login">Login</a></p>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>