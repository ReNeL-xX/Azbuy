<?php
class Database {
    private string $host = "localhost";
    private string $username = "u536627044_Azbuy";
    private string $password = "Azbuy101101";
    private string $database = "u536627044_Azbuy";  // CHANGE THIS to azbuy_db

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

        return $conn;
    }
}
?>