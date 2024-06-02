<?php
// controllers/ConfigController.php

class ConfigController {
    private $config;

    public function __construct() {
        $this->config = require '../config.php';
    }

    public function get($key) {
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }

    public function set($key, $value) {
        $this->config[$key] = $value;
    }

    public function isDebugMode() {
        return $this->get('debug');
    }

    public function applyDebugMode() {
        if ($this->isDebugMode()) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            error_reporting(0);
        }
    }
}
?>
