<?php
require_once "db.php";

$db = new Database();
$conn = $db->getConnection();

$result = $conn->query("SELECT * FROM suppliers ORDER BY name ASC");

$suppliers = [];
while ($row = $result->fetch_assoc()) {
    $suppliers[] = $row;
}

echo json_encode($suppliers);
