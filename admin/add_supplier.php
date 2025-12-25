<?php
require_once "db.php";

header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'] ?? '';
$contact = $data['contact'] ?? '';
$email = $data['email'] ?? '';
$phone = $data['phone'] ?? '';
$address = $data['address'] ?? '';

if (!$name || !$contact || !$email) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO suppliers (name, contact_person, email, phone, address) VALUES (?,?,?,?,?)"
);
$stmt->bind_param("sssss", $name, $contact, $email, $phone, $address);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add supplier"]);
}
