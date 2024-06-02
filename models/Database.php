<?php
// models/Database.php

require_once '../controllers/ConfigController.php';

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        $configController = new ConfigController();
        $this->host = $configController->get('db_host');
        $this->db_name = $configController->get('db_name');
        $this->username = $configController->get('db_user');
        $this->password = $configController->get('db_pass');
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
