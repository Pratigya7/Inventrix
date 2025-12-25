<?php
header("Content-Type: application/json");
include 'db.php';

$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);

$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products);
