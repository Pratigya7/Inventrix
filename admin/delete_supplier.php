<?php
require_once "db.php";
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

$stmt = $conn->prepare("DELETE FROM suppliers WHERE id=?");
$stmt->bind_param("i", $id);

echo json_encode(["success" => $stmt->execute()]);
