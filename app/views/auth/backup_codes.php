<?php 
$page_title = "Your Backup Codes";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AzBuy - Backup Codes</title>
    <link rel="stylesheet" href="/AzBuy/public/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .backup-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0A0A0A 0%, #1A1A1A 100%);
            padding: 2rem;
        }
        
        .backup-card {
            background: var(--dark-elevated);
            border-radius: 24px;
            padding: 2.5rem;
            max-width: 600px;
            width: 100%;
            border: 1px solid var(--border-color);
            text-align: center;
        }
        
        .codes-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin: 1.5rem 0;
        }
        
        .code-box {
            background: var(--dark-card);
            padding: 12px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 16px;
            letter-spacing: 1px;
            border: 1px solid var(--border-color);
            color: var(--primary-gold);
            font-weight: bold;
        }
        
        .alert-warning {
            background: rgba(255, 193, 7, 0.15);
            border: 1px solid rgba(255, 193, 7, 0.3);
            color: #ffc107;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.15);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #28a745;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin: 1.5rem 0;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #FFD700 0%, #DAA520 100%);
            color: #000;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
        }
        
        .btn-secondary {
            background: transparent;
            color: #FFD700;
            border: 2px solid #FFD700;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 215, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .btn-block {
            width: 100%;
        }
        
        .icon-large {
            font-size: 4rem;
            color: var(--primary-gold);
            margin-bottom: 1rem;
        }
        
        .warning-icon {
            color: #ffc107;
            margin-right: 8px;
        }
        
        @media (max-width: 480px) {
            .backup-card {
                padding: 1.5rem;
            }
            
            .codes-grid {
                grid-template-columns: 1fr;
            }
            
            .code-box {
                font-size: 14px;
                padding: 10px;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="backup-container">
        <div class="backup-card">
            <div class="icon-large">
                <i class="fas fa-save"></i>
            </div>
            
            <h1>Save Your Backup Codes!</h1>
            
            <div class="alert-warning">
                <i class="fas fa-exclamation-triangle warning-icon"></i> 
                <strong>IMPORTANT:</strong> These codes are your only way to recover your account if you lose your phone!
            </div>
            
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> 
                <strong>2FA Setup Complete!</strong> Your account is now secured.
            </div>
            
            <p>Each code can only be used once. Store them in a safe place.</p>
            
            <div class="codes-grid">
                <?php foreach ($backupCodes as $code): ?>
                    <div class="code-box"><?php echo htmlspecialchars($code); ?></div>
                <?php endforeach; ?>
            </div>
            
            <div class="button-group">
                <button class="btn btn-secondary" onclick="copyCodes()">
                    <i class="fas fa-copy"></i> Copy Codes
                </button>
                <button class="btn btn-secondary" onclick="downloadCodes()">
                    <i class="fas fa-download"></i> Download
                </button>
                <button class="btn btn-secondary" onclick="printCodes()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
            
            <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <a href="index.php?action=dashboard" class="btn btn-primary btn-block">
                    <i class="fas fa-home"></i> I've Saved My Codes → Go to Home
                </a>
            </div>
            
            <p style="margin-top: 1rem; font-size: 0.75rem; color: var(--text-muted);">
                <i class="fas fa-lock"></i> These codes will not be shown again. Please save them now.
            </p>
        </div>
    </div>
    
    <script>
        const codes = <?php echo json_encode($backupCodes); ?>;
        
        function copyCodes() {
            const codesText = codes.join('\n');
            navigator.clipboard.writeText(codesText).then(() => {
                showToast('Backup codes copied to clipboard!');
            }).catch(() => {
                alert('Failed to copy. Please manually copy the codes.');
            });
        }
        
        function downloadCodes() {
            const codesText = codes.join('\n');
            const content = `AZBUY BACKUP CODES
${'='.repeat(40)}

${codesText}

${'='.repeat(40)}
Keep these codes safe! Each code can only be used once.
Store them in a password manager or print them out.

Generated on: ${new Date().toLocaleString()}
`;
            const blob = new Blob([content], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'azbuy_backup_codes.txt';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
        
        function printCodes() {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>AzBuy Backup Codes</title>
                    <style>
                        body {
                            font-family: monospace;
                            text-align: center;
                            padding: 50px;
                            background: white;
                        }
                        h1 {
                            color: #DAA520;
                        }
                        .codes {
                            margin: 30px 0;
                            padding: 20px;
                            border: 2px solid #DAA520;
                            border-radius: 10px;
                            background: #f9f9f9;
                        }
                        .code {
                            font-size: 18px;
                            margin: 10px;
                            padding: 8px;
                            font-family: monospace;
                            font-weight: bold;
                        }
                        .warning {
                            color: #ffc107;
                            font-weight: bold;
                        }
                        hr {
                            margin: 20px 0;
                        }
                    </style>
                </head>
                <body>
                    <h1>🔐 AzBuy Backup Codes</h1>
                    <p>Keep these codes safe! Each code can only be used once.</p>
                    <hr>
                    <div class="codes">
                        ${codes.map(c => `<div class="code">${c}</div>`).join('')}
                    </div>
                    <hr>
                    <p class="warning">⚠️ Store these codes in a safe place. You won't see them again! ⚠️</p>
                    <p><small>Generated on: ${new Date().toLocaleString()}</small></p>
                </body>
                </html>
            `);
            printWindow.print();
            printWindow.close();
        }
        
        function showToast(message) {
            // Create toast element
            const toast = document.createElement('div');
            toast.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.background = 'var(--primary-gold)';
            toast.style.color = '#000';
            toast.style.padding = '12px 24px';
            toast.style.borderRadius = '50px';
            toast.style.zIndex = '9999';
            toast.style.fontSize = '14px';
            toast.style.fontWeight = 'bold';
            toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
            toast.style.animation = 'slideInRight 0.3s ease';
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Add animation style if not exists
        if (!document.querySelector('#toast-animation')) {
            const style = document.createElement('style');
            style.id = 'toast-animation';
            style.textContent = `
                @keyframes slideInRight {
                    from {
                        opacity: 0;
                        transform: translateX(100px);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
                }
            `;
            document.head.appendChild(style);
        }
    </script>
</body>
</html>