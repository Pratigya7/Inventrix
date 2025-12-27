<?php
require_once '../../admin/auth.php'; 
require_once '../../admin/db.php'; 

$data = json_decode(file_get_contents('php://input'), true);

$username = trim($data['username']);
$email = trim($data['email']);
$password = trim($data['password']);

$response = ['success'=>false, 'message'=>'Something went wrong'];

if(!$username || !$email) {
    $response['message'] = 'Username and Email are required';
    echo json_encode($response);
    exit;
}

$userId = $_SESSION['user_id'];

// Update password only if provided
if($password) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?");
    $stmt->bind_param("sssi", $username, $email, $hashed, $userId);
} else {
    $stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE id=?");
    $stmt->bind_param("ssi", $username, $email, $userId);
}

if($stmt->execute()) {
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $response['success'] = true;
    $response['message'] = 'Profile updated successfully';
} else {
    $response['message'] = 'Failed to update profile';
}

echo json_encode($response);
