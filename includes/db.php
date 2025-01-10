<?php
require_once '../config/config.php'; // Assicurati che il percorso sia corretto

if (!function_exists('connectDB')) {
    function connectDB() {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }
}
?>