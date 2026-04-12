<?php
class Database {
    private string $host = "localhost";
    private string $username = "root";
    private string $password = "";
    private string $database = "azbuy_db";

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