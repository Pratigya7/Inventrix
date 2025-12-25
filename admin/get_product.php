<?php
header("Content-Type: application/json");
include 'db.php';

$db = new Database();
$conn = $db->getConnection();

$id = intval($_GET['id'] ?? 0);

$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["success" => false, "message" => "Product not found"]);
}
