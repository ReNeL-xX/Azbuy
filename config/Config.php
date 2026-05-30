<?php

date_default_timezone_set('Asia/Manila'); 

class Database {
    private string $host = "localhost";
    private string $username = "u536627044_Azbuy";
    private string $password = "Azbuy101101";
    private string $database = "u536627044_Azbuy";

    public function connect(): mysqli {
        $conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );

        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
        return $conn;
    }
}
?>