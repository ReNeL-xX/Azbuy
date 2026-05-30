<?php 
$page_title = "2FA Setup Required";

// Hardcode base URL for Hostinger
$base_url = 'https://azbuy.bsit2a.com/public';

// Get the secret and email from session
$secret = $_SESSION['2fa_temp_secret'] ?? '';
$email = $_SESSION['2fa_pending_user_data']['email'] ?? '';

// Generate QR code
$twoFactorAuth = new TwoFactorAuth();
$qrCodeHtml = $twoFactorAuth->generateQRCodeHtml($email, $secret);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AzBuy - 2FA Setup Required</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0A0A0A 0%, #1A1A1A 100%);
            min-height: 100vh;
        }
        
        .verification-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        
        .verification-card {
            background: #1A1A1A;
            border-radius: 24px;
            padding: 2rem;
            max-width: 450px;
            width: 100%;
            border: 1px solid rgba(255, 215, 0, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        
        .verification-card h2 {
            color: #FFD700;
            margin-bottom: 0.5rem;
            font-size: 1.3rem;
        }
        
        .verification-card > p {
            color: #B0B0B0;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }
        
        .qr-section {
            text-align: center;
            margin: 1rem 0;
        }
        
        .qr-code-wrapper {
            background: white;
            padding: 1rem;
            border-radius: 20px;
            display: inline-block;
            margin: 0 auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .qr-code-wrapper img {
            width: 200px;
            height: 200px;
            display: block;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #FFFFFF;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .form-group label i {
            color: #FFD700;
            margin-right: 8px;
        }
        
        .code-input {
            width: 100%;
            padding: 14px;
            background: #0A0A0A;
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 12px;
            color: #FFFFFF;
            font-size: 1.5rem;
            text-align: center;
            letter-spacing: 8px;
            font-family: monospace;
        }
        
        .code-input:focus {
            outline: none;
            border-color: #FFD700;
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #FFD700 0%, #DAA520 100%);
            color: #000000;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            width: 100%;
            font-size: 1rem;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
        }
        
        .icon-center {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .timer {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.75rem;
            color: #B0B0B0;
        }
        
        .alert-warning {
            background: rgba(255, 193, 7, 0.15);
            border: 1px solid rgba(255, 193, 7, 0.3);
            color: #ffc107;
            padding: 0.8rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
            font-size: 0.75rem;
        }
        
        .alert-warning i {
            font-size: 1rem;
        }
        
        .alert-warning strong {
            display: block;
        }
        
        .app-buttons {
            display: flex;
            gap: 0.8rem;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        
        .app-btn {
            color: #FFD700;
            text-decoration: none;
            font-size: 0.7rem;
            padding: 5px 10px;
            border-radius: 50px;
            background: rgba(255, 215, 0, 0.1);
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: 1px solid rgba(255, 215, 0, 0.2);
        }
        
        .app-btn:hover {
            background: rgba(255, 215, 0, 0.2);
            transform: translateY(-2px);
        }
        
        @media (max-width: 480px) {
            .verification-card {
                padding: 1.5rem;
            }
            
            .qr-code-wrapper img {
                width: 160px;
                height: 160px;
            }
            
            .code-input {
                font-size: 1.2rem;
                letter-spacing: 5px;
            }
            
            .app-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .app-btn {
                width: 80%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-card">
            <div class="alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>2FA Required!</strong>
                    <small>Set up two-factor authentication before accessing your account</small>
                </div>
            </div>
            
            <div class="app-buttons">
                <a href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank" class="app-btn">
                    <i class="fab fa-apple"></i> App Store
                </a>
                <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="app-btn">
                    <i class="fab fa-google-play"></i> Google Play
                </a>
            </div>
            
            <div class="icon-center">
                <i class="fas fa-qrcode" style="font-size: 2rem; color: #FFD700;"></i>
            </div>
            <h2>Setup Google Authenticator</h2>
            <p>Scan the QR code with your authenticator app</p>
            
            <div class="qr-section">
                <div class="qr-code-wrapper">
                    <?php echo $qrCodeHtml; ?>
                </div>
            </div>
            
            <form action="index.php?action=2fa-setup-process" method="POST">
                <div class="form-group">
                    <label><i class="fas fa-key"></i> Verification Code</label>
                    <input type="text" name="code" maxlength="6" pattern="[0-9]{6}" required 
                           class="code-input" placeholder="000000" autofocus autocomplete="off">
                </div>
                
                <button type="submit" class="btn-primary">Verify & Complete Setup</button>
            </form>
            
            <div class="timer" id="timer"></div>
        </div>
    </div>
    
    <script>
        let timeLeft = 30;
        const timerElement = document.getElementById('timer');
        
        if (timerElement) {
            function updateTimer() {
                if (timeLeft <= 0) {
                    timerElement.innerHTML = '<i class="fas fa-sync-alt"></i> Generate a new code in your authenticator app';
                    timerElement.style.color = '#ffc107';
                    timeLeft = 30;
                } else {
                    timerElement.innerHTML = '<i class="fas fa-clock"></i> Code refreshes in ' + timeLeft + ' seconds';
                    timeLeft--;
                    setTimeout(updateTimer, 1000);
                }
            }
            updateTimer();
        }
        
        const codeInput = document.querySelector('input[name="code"]');
        if (codeInput) {
            codeInput.addEventListener('input', function(e) {
                if (this.value.length === 6 && /^\d+$/.test(this.value)) {
                    this.form.submit();
                }
            });
        }
    </script>
</body>
</html>