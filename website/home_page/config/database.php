<?php
class Database {
    private $host = '127.0.0.1';
    private $db_name = 'project';
    private $username = 'newuser';
    private $password = 'password';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
        if ($this->conn->connect_error) {
            die('Connection failed: ' . $this->conn->connect_error);
        }
        $this->conn->set_charset('utf8mb4');
        return $this->conn;
    }
} 