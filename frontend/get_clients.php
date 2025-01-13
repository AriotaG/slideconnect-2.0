<?php
require_once '../../includes/db.php';

$conn = connectDB();
$result = $conn->query("SELECT id, name FROM clients");

$options = "";
while ($row = $result->fetch_assoc()) {
    $options .= "<option value='{$row['id']}'>{$row['name']}</option>";
}

echo $options;
?>