<?php
session_start();
require_once 'db.php';

$db = new Database();
$conn = $db->getConnection();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields!";
    } else {
        $stmt = $conn->prepare(
            "SELECT id, full_name, email, password, role 
             FROM users 
             WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
               
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_name']  = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role']  = $user['role'];

                
                if ($user['role'] === 'admin') {
                    header("Location: /Inventrix/admin/admin_dashboard.php");
                } else {
                    header("Location: /Inventrix/users/user_dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid email or password!";
            }
        } else {
            $error = "Invalid email or password!";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventora - Login</title>
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>

<div class="container">
    <div class="logo">
        <h1>Inventora</h1>
        <p>Welcome back! Please sign in</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="error-message" style="color:#e74c3c;text-align:center;margin-bottom:15px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>


    <form id="loginForm" method="POST" action="">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <div class="password-container">
                <input type="password" name="password" id="password" required>
            </div>
        </div>

        <button type="submit" class="submit-btn">Sign In</button>

        <div class="signup-link">
            Don’t have an account? <a href="signup.php">Sign Up</a>
        </div>
    </form>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    const pwd = document.getElementById('password');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
});
</script>

</body>
</html>
