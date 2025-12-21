<?php
session_start();
require_once 'db.php';

checkRole(['admin']); // Only admin can access

$db = new Database();
$conn = $db->getConnection();

header('Content-Type: application/json');

$sql = "SELECT id, full_name, email, role FROM users ORDER BY id DESC";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode($users);
