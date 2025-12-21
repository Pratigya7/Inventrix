<?php
session_start();
require_once 'db.php';
checkRole(['admin']);
$db = new Database();
$conn = $db->getConnection();
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id'] ?? 0);
$full_name = $data['full_name'] ?? '';
$role = $data['role'] ?? '';

if(!$id || !$full_name || !$role) { echo json_encode(['error'=>'Missing fields']); exit; }

$stmt = $conn->prepare("UPDATE users SET full_name=?, role=? WHERE id=?");
$stmt->bind_param("ssi", $full_name, $role, $id);
if($stmt->execute()) echo json_encode(['success'=>'User updated successfully']);
else echo json_encode(['error'=>'Failed to update user']);
