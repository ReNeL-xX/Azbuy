<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PragmaRX\Google2FAQRCode\Google2FA;

class TwoFactorAuth {
    private Google2FA $google2fa;
    private string $appName;
    
    public function __construct() {
        $this->google2fa = new Google2FA();
        $this->appName = 'AzBuy';
    }
    
    public function generateSecret(): string {
        return $this->google2fa->generateSecretKey();
    }
    
    // Generate QR Code HTML (works for sure)
    public function generateQRCodeHtml(string $email, string $secret): string {
        return $this->google2fa->getQRCodeInline(
            $this->appName,
            $email,
            $secret
        );
    }
    
    // Alias for generateQRCodeHtml for backward compatibility
    public function generateQRCodeBase64(string $email, string $secret): string {
        return $this->generateQRCodeHtml($email, $secret);
    }
    
    public function verifyCode(string $secret, string $code): bool {
        $code = trim(preg_replace('/\s+/', '', $code));
        return $this->google2fa->verifyKey($secret, $code, 2);
    }
    
    public function generateBackupCodes(int $count = 10): array {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = sprintf('%04d-%04d', random_int(0, 9999), random_int(0, 9999));
        }
        return $codes;
    }
    
    public function verifyBackupCode(string $inputCode, array $savedCodes): bool {
        if (empty($savedCodes)) return false;
        $inputCode = trim($inputCode);
        foreach ($savedCodes as $savedCode) {
            if (hash_equals($savedCode, $inputCode)) {
                return true;
            }
        }
        return false;
    }
}