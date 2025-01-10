<?php
require_once 'db.php';
require_once 'auth.php';

if (!function_exists('connectDB')) {
    function connectDB() {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }
}

function getDoorById($door_id) {
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT d.*, u.username AS user_name FROM doors d LEFT JOIN users u ON d.user_id = u.id WHERE d.id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $door_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $door = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $door;
}

function getAllDoorsExcept($door_id) {
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT id, name FROM doors WHERE id != ?");
    $stmt->bind_param("i", $door_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $doors = [];
    while ($row = $result->fetch_assoc()) {
        $doors[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $doors;
}

function getAllUsers() {
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT id, username FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $users;
}

function getVideoFiles($dir) {
    if (is_dir($dir)) {
        return array_diff(scandir($dir), array('..', '.'));
    }
    return [];
}

function getScheduleByDoorId($door_id) {
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT * FROM schedule WHERE door_id = ? ORDER BY start_time");
    $stmt->bind_param("i", $door_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $schedule = [];
    while ($row = $result->fetch_assoc()) {
        $schedule[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $schedule;
}
?>