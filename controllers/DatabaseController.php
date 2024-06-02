<?php
// controllers/DatabaseController.php

require_once '../models/Database.php';

class DatabaseController {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function connect() {
        return $this->db->getConnection();
    }
}
?>
