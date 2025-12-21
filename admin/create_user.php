<?php
session_start();
require_once 'db.php';
checkRole(['admin']);

$db = new Database();
$conn = $db->getConnection();

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$name = sanitize($data['full_name'] ?? '');
$email = sanitize($data['email'] ?? '');
$role = sanitize($data['role'] ?? '');
$password = $data['password'] ?? '';

if (!$name || !$email || !$password || !$role) {
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Invalid email address']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['error' => 'Password must be at least 6 characters']);
    exit;
}

// Check duplicate email
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['error' => 'Email already exists']);
    exit;
}
$stmt->close();

// Insert user
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare(
    "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

if ($stmt->execute()) {
    echo json_encode([
        'success' => 'User created successfully',
        'user' => [
            'id' => $stmt->insert_id,
            'full_name' => $name,
            'email' => $email,
            'role' => $role
        ]
    ]);
} else {
    echo json_encode(['error' => 'Failed to create user']);
}

$stmt->close();
?>
