<?php
require_once "db.php";
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$name = $data['name'];
$contact = $data['contact'];
$email = $data['email'];
$phone = $data['phone'];
$address = $data['address'];

$stmt = $conn->prepare(
    "UPDATE suppliers SET name=?, contact_person=?, email=?, phone=?, address=? WHERE id=?"
);
$stmt->bind_param("sssssi", $name, $contact, $email, $phone, $address, $id);

echo json_encode(["success" => $stmt->execute()]);
