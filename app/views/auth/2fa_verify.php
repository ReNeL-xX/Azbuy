<?php 
$page_title = "Two-Factor Authentication";

// Hardcode base URL for Hostinger
$base_url = 'https://azbuy.bsit2a.com/public';

// Get the user data from session
$email = $_SESSION['2fa_pending_user_data']['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AzBuy - 2FA Verification</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
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
            max-width: 450px;
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
        
        .verification-card p {
            color: #B0B0B0;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }
        
        .icon-center {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1rem;
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
            text-align: left;
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
        
        .code-input::placeholder {
            letter-spacing: 5px;
            font-size: 1rem;
            color: #444;
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
        
        .backup-link {
            margin-top: 1rem;
        }
        
        .backup-link a {
            color: #FFD700;
            text-decoration: none;
            font-size: 0.8rem;
        }
        
        .backup-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .verification-card {
                padding: 1.5rem;
            }
            
            .code-input {
                font-size: 1.2rem;
                letter-spacing: 5px;
                padding: 12px;
            }
            
            .verification-card h2 {
                font-size: 1.2rem;
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
            
            <form action="index.php?action=2fa-verify-process" method="POST">
                <div class="form-group">
                    <label><i class="fas fa-key"></i> Authentication Code</label>
                    <input type="text" name="code" maxlength="6" pattern="[0-9]{6}" required 
                           class="code-input" placeholder="000000" autofocus autocomplete="off">
                </div>
                
                <button type="submit" class="btn-primary">Verify & Login</button>
            </form>
            
            <div class="backup-link">
                <a href="javascript:void(0)" onclick="showBackupCodeInput()">Use a backup code instead</a>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-submit when 6 digits are entered
        const codeInput = document.querySelector('input[name="code"]');
        if (codeInput) {
            codeInput.addEventListener('input', function(e) {
                if (this.value.length === 6 && /^\d+$/.test(this.value)) {
                    this.form.submit();
                }
            });
        }
        
        function showBackupCodeInput() {
            const formGroup = document.querySelector('.form-group');
            const label = formGroup.querySelector('label');
            label.innerHTML = '<i class="fas fa-key"></i> Backup Code (XXXX-XXXX)';
            
            const input = formGroup.querySelector('input');
            input.placeholder = '1234-5678';
            input.maxLength = 9;
            input.pattern = '[0-9]{4}-[0-9]{4}';
            
            const backupLink = document.querySelector('.backup-link');
            backupLink.style.display = 'none';
        }
    </script>
</body>
</html>