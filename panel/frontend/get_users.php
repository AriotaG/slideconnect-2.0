<?php
require_once '../../includes/db.php';

$conn = connectDB();
$result = $conn->query("SELECT id, username, role FROM users");

$rows = "";
while ($row = $result->fetch_assoc()) {
    $rows .= "<tr>
                <td>{$row['id']}</td>
                <td>{$row['username']}</td>
                <td>{$row['role']}</td>
                <td>
                  <a href='edit_user.php?id={$row['id']}' class='btn btn-sm btn-primary'>Edit</a>
                  <a href='delete_user.php?id={$row['id']}' class='btn btn-sm btn-danger'>Delete</a>
                </td>
              </tr>";
}

echo $rows;
?>