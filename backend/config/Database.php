<?php
class Database {
    private $host = 'localhost';
    private $db_user = 'root';
    private $db_pass = '';
    private $db_name = 'biometric_verification';
    private $conn;

    public function connect() {
        $this->conn = new mysqli(
            $this->host,
            $this->db_user,
            $this->db_pass,
            $this->db_name
        );

        if ($this->conn->connect_error) {
            die('Connection Error: ' . $this->conn->connect_error);
        }

        $this->conn->set_charset("utf8mb4");
        return $this->conn;
    }

    public function query($sql) {
        return $this->conn->query($sql);
    }

    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    public function escape($data) {
        return $this->conn->real_escape_string($data);
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>