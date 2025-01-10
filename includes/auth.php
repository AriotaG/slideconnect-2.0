<?php
session_start();
require_once 'db.php';

function login($username, $password) {
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $_SESSION['user'] = $result->fetch_assoc();
        return true;
    } else {
        return false;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function logout() {
    session_destroy();
}
?>