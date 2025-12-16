<?php
session_start();
require_once 'db.php';

$db = new Database();
$conn = $db->getConnection();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    // Validation
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields!";
    } else {
        // Check user
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // session ma user ko info rakhxa
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: admin_dashboard.php");
                        break;
                    case 'user':
                        header("Location: ../users./user_dashboard.php");
                        break;
                    default:
                        header("Location: dashboard.php");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventora - Login</title>
    <link rel="stylesheet" href="./css/login.css">
    
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>Inventora</h1>
            <p>Welcome back! Please sign in</p>
        </div>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" required>
                <div class="error" id="emailError">Please enter your email</div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" required>
                    <button type="button" class="toggle-password" id="togglePassword">👁️</button>
                </div>
                <div class="error" id="passwordError">Please enter your password</div>
            </div>

            <div class="remember-forgot">
                <div class="remember">
                    <input type="checkbox" id="rememberMe">
                    <label for="rememberMe">Remember me</label>
                </div>
                <a href="#" class="forgot">Forgot password?</a>
            </div>

            <button type="submit" class="submit-btn">Sign In</button>

            <div class="success" id="successMsg">Login successful! Redirecting...</div>

            <div class="signup-link">
                Don't have an account? <a href="signup.php">Sign Up</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            toggleBtn.addEventListener('click', function() {
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                this.textContent = type === 'password' ? '👁️' : '👁️‍🗨️';
            });

            // Auto-fill email from signup
            const lastEmail = localStorage.getItem('lastSignedUpEmail');
            if (lastEmail) {
                document.getElementById('email').value = lastEmail;
                localStorage.removeItem('lastSignedUpEmail');
            }

            // Load demo accounts on first run
            const users = JSON.parse(localStorage.getItem('users') || '[]');
            if (users.length === 0) {
                const demoUsers = [
                    {email: 'admin@inventora.com', password: 'admin123', role: 'admin', name: 'Admin User'},
                    {email: 'user@inventora.com', password: 'user123', role: 'user', name: 'Regular User'},
                    {email: 'viewer@inventora.com', password: 'viewer123', role: 'viewer', name: 'Viewer User'}
                ];
                localStorage.setItem('users', JSON.stringify(demoUsers));
            }

            // Form submission
            const form = document.getElementById('loginForm');
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Reset messages
                document.querySelectorAll('.error').forEach(el => el.style.display = 'none');
                document.getElementById('successMsg').style.display = 'none';
                
                // Get values
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value;
                const rememberMe = document.getElementById('rememberMe').checked;
                
                // Validate
                if (!email || !password) {
                    document.getElementById('emailError').style.display = email ? 'none' : 'block';
                    document.getElementById('passwordError').style.display = password ? 'none' : 'block';
                    return;
                }
                
                // Check credentials (in production, this would be a server request)
                const users = JSON.parse(localStorage.getItem('users') || '[]');
                const user = users.find(u => u.email === email && u.password === password);
                
                if (user) {
                    // Login successful
                    document.getElementById('successMsg').style.display = 'block';
                    
                    // Store session (in production, use proper authentication)
                    sessionStorage.setItem('currentUser', JSON.stringify(user));
                    
                    if (rememberMe) {
                        localStorage.setItem('rememberedEmail', email);
                    }
                    
                    // Redirect based on role after 1 second
                    setTimeout(() => {
                        window.location.href = getDashboardUrl(user.role);
                    }, 1000);
                } else {
                    document.getElementById('passwordError').textContent = 'Invalid email or password';
                    document.getElementById('passwordError').style.display = 'block';
                }
            });
            
            function getDashboardUrl(role) {
                switch(role) {
                    case 'admin': return 'admin-dashboard.php';
                    case 'user': return 'user-dashboard.php';
                    case 'viewer': return 'viewer-dashboard.html';
                    default: return 'dashboard.html';
                }
            }
        });
    </script>
</body>
</html>