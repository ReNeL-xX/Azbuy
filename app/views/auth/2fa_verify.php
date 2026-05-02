<?php 
$page_title = "Two-Factor Authentication";

// Get the secret for QR code generation
$secret = $_SESSION['2fa_pending_user_data']['two_factor_secret'] ?? '';
$email = $_SESSION['2fa_pending_user_data']['email'] ?? '';

// Generate QR code using the package
$twoFactorAuth = new TwoFactorAuth();
$qrCodeHtml = $twoFactorAuth->generateQRCodeHtml($email, $secret);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AzBuy - 2FA Verification</title>
    <link rel="stylesheet" href="/AzBuy/public/assets/css/style.css">
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
            padding: 2.5rem;
            max-width: 500px;
            width: 100%;
            border: 1px solid rgba(255, 215, 0, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        
        .verification-card h2 {
            color: #FFD700;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }
        
        .verification-card > p {
            color: #B0B0B0;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
        
        .qr-section {
            text-align: center;
            margin: 1rem 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .qr-code-wrapper {
            background: white;
            padding: 1rem;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .qr-code-wrapper img {
            width: 220px;
            height: 220px;
            display: block;
            margin: 0 auto;
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
            padding: 15px;
            background: #0A0A0A;
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 12px;
            color: #FFFFFF;
            font-size: 2rem;
            text-align: center;
            letter-spacing: 10px;
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
            padding: 14px 28px;
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
            font-size: 0.8rem;
            color: #B0B0B0;
        }
        
        @media (max-width: 480px) {
            .verification-card {
                padding: 1.5rem;
            }
            
            .qr-code-wrapper {
                padding: 0.8rem;
            }
            
            .qr-code-wrapper img {
                width: 180px;
                height: 180px;
            }
            
            .code-input {
                font-size: 1.2rem;
                letter-spacing: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-card">
            <div class="icon-center">
                <i class="fas fa-shield-alt" style="font-size: 2.5rem; color: #FFD700;"></i>
            </div>
            <h2>Two-Factor Authentication</h2>
            <p>Open Google Authenticator and enter the 6-digit code</p>
            
            <div class="qr-section">
                <div class="qr-code-wrapper">
                    <?php echo $qrCodeHtml; ?>
                </div>
            </div>
            
            <form action="index.php?action=2fa-verify-process" method="POST">
                <div class="form-group">
                    <label><i class="fas fa-key"></i> Authentication Code</label>
                    <input type="text" name="code" maxlength="6" pattern="[0-9]{6}" required 
                           class="code-input" placeholder="000000" autofocus autocomplete="off">
                </div>
                
                <button type="submit" class="btn-primary">Verify & Login</button>
            </form>
            
            <div class="timer" id="timer"></div>
        </div>
    </div>
    
    <script>
        // Timer for code refresh
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
        
        // Auto-submit when 6 digits are entered
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