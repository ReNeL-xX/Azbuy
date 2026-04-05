<?php 
$page_title = "Login";
ob_start();
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-gavel"></i>
            <h2>Welcome Back to Azbuy</h2>
            <p>Sign in to continue your bidding journey</p>
        </div>
        
        <form action="index.php?action=login-process" method="POST">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Username or Email</label>
                <input type="text" name="username" required placeholder="Enter your username or email">
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" required placeholder="Enter your password">
            </div>
            
            <div class="form-checkbox">
                <input type="checkbox" id="remember">
                <label for="remember">Remember me</label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        
        <div class="auth-divider">
            <span>or</span>
        </div>
        
        <div class="social-login">
            <button class="btn-social google" type="button"><i class="fab fa-google"></i> Google</button>
            <button class="btn-social facebook" type="button"><i class="fab fa-facebook-f"></i> Facebook</button>
        </div>
        
        <p class="auth-link">Don't have an account? <a href="index.php?action=register">Sign Up</a></p>
      
        <!-- Demo credentials info -->
        <div class="demo-info" style="margin-top: 1.5rem; padding: 1rem; background: rgba(255,215,0,0.1); border-radius: 8px; text-align: center;">
            <small style="color: #FFD700;">Demo Credentials:</small><br>
            <small>Username: <strong>demo</strong> | Password: <strong>demo123</strong></small>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>