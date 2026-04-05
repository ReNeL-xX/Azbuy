<?php
class Config {
    public static $SITE_NAME = "Azbuy";
    public static $SITE_URL = "http://localhost/azbuy";
    public static $TIMEZONE = "America/New_York";
    
    public static function init() {
        date_default_timezone_set(self::$TIMEZONE);
        session_start();
    }
}