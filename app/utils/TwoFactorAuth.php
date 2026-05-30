<?php
class TwoFactorAuth {
    private string $appName;
    
    public function __construct() {
        $this->appName = 'AzBuy';
    }
    
    public function generateSecret(): string {
        // Generate a random 16-character base32 secret
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < 16; $i++) {
            $secret .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $secret;
    }
    
    public function generateQRCodeHtml(string $email, string $secret): string {
        // Build TOTP URL for Google Authenticator
        $totp_url = 'otpauth://totp/' . urlencode($this->appName . ':' . $email) . 
                   '?secret=' . $secret . 
                   '&issuer=' . urlencode($this->appName);
        
        // Use QuickChart API (free, works without any PHP dependencies)
        $qr_url = 'https://quickchart.io/qr?text=' . urlencode($totp_url) . '&size=200&margin=2';
        
        return '<img src="' . $qr_url . '" alt="QR Code" style="width:200px;height:200px;">';
    }
    
    public function verifyCode(string $secret, string $code): bool {
        $code = trim(preg_replace('/\s+/', '', $code));
        
        // TOTP: codes change every 30 seconds
        $timestamp = floor(time() / 30);
        
        // Check current and adjacent time windows (allow 1 step drift)
        for ($i = -1; $i <= 1; $i++) {
            $calculated = $this->generateTOTP($secret, $timestamp + $i);
            if ($calculated === $code) {
                return true;
            }
        }
        return false;
    }
    
    private function generateTOTP($secret, $timestamp): string {
        // Decode base32 secret
        $secret_decoded = $this->base32Decode($secret);
        
        // Pack timestamp into binary string (8 bytes, big-endian)
        $time = pack('N', $timestamp);
        $time = str_pad($time, 8, "\0", STR_PAD_LEFT);
        
        // Calculate HMAC-SHA1 hash
        $hash = hash_hmac('sha1', $time, $secret_decoded, true);
        
        // Get offset and generate OTP
        $offset = ord($hash[19]) & 0xf;
        $otp = (
            ((ord($hash[$offset]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;
        
        return str_pad($otp, 6, '0', STR_PAD_LEFT);
    }
    
    private function base32Decode($input): string {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $input = strtoupper($input);
        $output = '';
        $buffer = 0;
        $bits = 0;
        
        for ($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];
            $val = strpos($alphabet, $char);
            if ($val === false) continue;
            
            $buffer = ($buffer << 5) | $val;
            $bits += 5;
            
            if ($bits >= 8) {
                $bits -= 8;
                $output .= chr(($buffer >> $bits) & 0xFF);
            }
        }
        return $output;
    }
}
?>