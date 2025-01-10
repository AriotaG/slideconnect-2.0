<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user']['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? md5($_POST['password']) : $_SESSION['user']['password'];
    $profile_image = $_FILES['profile_image']['name'];

    // Upload profile image if provided
    if (!empty($profile_image)) {
        $target_dir = "../uploads/profile_images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($profile_image);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file);
    } else {
        $profile_image = $_SESSION['user']['profile_image'];
    }

    $conn = connectDB();
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, profile_image = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $email, $password, $profile_image, $user_id);
    $stmt->execute();

    // Update session data
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['password'] = $password;
    $_SESSION['user']['profile_image'] = $profile_image;

    header("Location: user_profile.php");
    exit();
}
?>