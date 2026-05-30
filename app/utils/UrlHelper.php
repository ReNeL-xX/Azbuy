<?php
class UrlHelper {
    private static $base_url = null;
    
    public static function getBaseUrl() {
        if (self::$base_url === null) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            
            // For your structure: https://azbuy.bsit2a.com/public
            $script_name = $_SERVER['SCRIPT_NAME'];
            // Remove index.php from the path
            $path = rtrim(str_replace('index.php', '', $script_name), '/');
            
            self::$base_url = $protocol . '://' . $host . $path;
        }
        return self::$base_url;
    }
    
    public static function asset($path) {
        return self::getBaseUrl() . '/' . ltrim($path, '/');
    }
    
    public static function imageUrl($image_path) {
        if (empty($image_path)) {
            return self::asset('assets/images/placeholder.jpg');
        }
        // Remove any 'public/' prefix that might be in the database
        $clean_path = ltrim($image_path, '/');
        $clean_path = str_replace('public/', '', $clean_path);
        return self::getBaseUrl() . '/' . $clean_path;
    }
}
?>