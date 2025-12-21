<?php
session_start();
require_once 'db.php';
checkRole(['admin']);
$db = new Database();
$conn = $db->getConnection();
header('Content-Type: application/json');

$id = intval($_GET['id'] ?? 0);
if(!$id) { echo json_encode(['error'=>'Invalid ID']); exit; }

$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i",$id);
if($stmt->execute()) echo json_encode(['success'=>'User deleted successfully']);
else echo json_encode(['error'=>'Failed to delete user']);
