<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file = $_FILES['file'];
    $allowed_types = ['video/mp4', 'video/avi', 'video/mov', 'video/wmv'];
    $max_size = 60 * 1024 * 1024; // 60MB

    if ($file['error'] == UPLOAD_ERR_OK) {
        if (!in_array($file['type'], $allowed_types)) {
            echo "Invalid file type: " . $file['type'];
            exit();
        }
        if ($file['size'] > $max_size) {
            echo "File size exceeds limit: " . $file['size'];
            exit();
        }

        $file_path = $upload_dir . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            echo "File uploaded successfully: " . $file_path;
        } else {
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "File upload error: " . $file['error'];
    }
} else {
    echo "Invalid request method.";
}
?>