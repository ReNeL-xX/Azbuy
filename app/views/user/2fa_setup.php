<?php 
$page_title = "Two-Factor Authentication Setup";
ob_start();
?>

<div class="settings-container">
    <h1><i class="fas fa-shield-alt"></i> Two-Factor Authentication</h1>
    
    <div class="settings-grid">
        <div class="settings-sidebar">
            <div class="profile-summary">
                <div class="profile-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3>2FA Settings</h3>
                <p>Secure your account</p>
            </div>
            <div class="settings-nav">
                <a href="index.php?action=settings" class="active">
                    <i class="fas fa-arrow-left"></i> Back to Settings
                </a>
            </div>
        </div>
        
        <div class="settings-content">
            <?php if (!$twoFactorStatus['enabled']): ?>
                <!-- Enable 2FA -->
                <div class="setup-2fa">
                    <h2><i class="fas fa-qrcode"></i> Setup Google Authenticator</h2>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Two-factor authentication adds an extra layer of security to your account.
                    </div>
                    
                    <div class="qr-section" style="text-align: center; margin: 2rem 0;">
                        <h3>Step 1: Scan QR Code</h3>
                        <div class="qr-code" style="margin: 1rem auto; display: inline-block;">
                            <img src="<?php echo $qrCodeBase64; ?>" alt="2FA QR Code" style="border-radius: 12px; border: 2px solid var(--primary-gold);">
                        </div>
                        <p>Or enter this code manually:</p>
                        <code style="background: var(--dark-card); padding: 10px; display: inline-block; border-radius: 8px; font-size: 16px; letter-spacing: 2px;">
                            <?php echo chunk_split($secret, 4, ' '); ?>
                        </code>
                    </div>
                    
                    <div class="backup-codes-section" style="background: var(--dark-card); border-radius: 16px; padding: 1.5rem; margin: 1rem 0;">
                        <h3>Step 2: Save Backup Codes</h3>
                        <p>These codes can be used to access your account if you lose your phone.</p>
                        <div class="backup-codes" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 1rem 0;">
                            <?php foreach ($backupCodes as $code): ?>
                                <code style="background: var(--dark-elevated); padding: 8px; text-align: center; border-radius: 8px; font-family: monospace;"><?php echo $code; ?></code>
                            <?php endforeach; ?>
                        </div>
                        <button class="btn btn-secondary" onclick="copyBackupCodes()">
                            <i class="fas fa-copy"></i> Copy Codes
                        </button>
                        <button class="btn btn-secondary" onclick="downloadBackupCodes()">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </div>
                    
                    <div class="verification-section" style="margin: 2rem 0;">
                        <h3>Step 3: Verify Setup</h3>
                        <form action="index.php?action=2fa-enable" method="POST">
                            <div class="form-group">
                                <label>Enter 6-digit code from Google Authenticator</label>
                                <input type="text" name="code" maxlength="6" pattern="[0-9]{6}" required placeholder="000000">
                            </div>
                            <button type="submit" class="btn btn-primary">Enable 2FA</button>
                        </form>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Disable 2FA -->
                <div class="disable-2fa">
                    <h2><i class="fas fa-check-circle"></i> 2FA is Enabled</h2>
                    
                    <div class="alert alert-success">
                        <i class="fas fa-shield-alt"></i> 
                        Your account is protected by two-factor authentication.
                    </div>
                    
                    <?php if (!empty($backupCodes)): ?>
                        <div class="backup-codes-section" style="background: var(--dark-card); border-radius: 16px; padding: 1.5rem; margin: 1rem 0;">
                            <h3>Your Backup Codes</h3>
                            <p>Keep these codes in a safe place. Each code can only be used once.</p>
                            <div class="backup-codes" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 1rem 0;">
                                <?php foreach ($backupCodes as $code): ?>
                                    <code style="background: var(--dark-elevated); padding: 8px; text-align: center; border-radius: 8px;"><?php echo $code; ?></code>
                                <?php endforeach; ?>
                            </div>
                            <button class="btn btn-secondary" onclick="copyBackupCodes()">
                                <i class="fas fa-copy"></i> Copy Codes
                            </button>
                            <button class="btn btn-secondary" onclick="downloadBackupCodes()">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="disable-section" style="margin: 2rem 0; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                        <h3>Disable Two-Factor Authentication</h3>
                        <p>Enter your 2FA code to disable this feature.</p>
                        <form action="index.php?action=2fa-disable" method="POST">
                            <div class="form-group">
                                <label>6-digit code from Google Authenticator</label>
                                <input type="text" name="code" maxlength="6" pattern="[0-9]{6}" required placeholder="000000">
                            </div>
                            <button type="submit" class="btn btn-danger">Disable 2FA</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function copyBackupCodes() {
    const codes = document.querySelectorAll('.backup-codes code');
    const codesText = Array.from(codes).map(code => code.innerText).join('\n');
    
    navigator.clipboard.writeText(codesText).then(() => {
        showToast('Backup codes copied to clipboard!');
    });
}

function downloadBackupCodes() {
    const codes = document.querySelectorAll('.backup-codes code');
    const codesText = Array.from(codes).map(code => code.innerText).join('\n');
    
    const blob = new Blob([`AzBuy Backup Codes\n${'='.repeat(30)}\n\n${codesText}\n\nKeep these codes safe!`], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'azbuy_backup_codes.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.background = 'var(--primary-gold)';
    toast.style.color = '#000';
    toast.style.padding = '12px 24px';
    toast.style.borderRadius = '50px';
    toast.style.zIndex = '9999';
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>

<style>
.alert-info {
    background: rgba(23, 162, 184, 0.15);
    border: 1px solid rgba(23, 162, 184, 0.3);
    color: #17a2b8;
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

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
    transform: translateY(-2px);
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>