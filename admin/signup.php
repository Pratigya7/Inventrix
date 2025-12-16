<?php
session_start();
require_once 'db.php';

$db = new Database();
$conn = $db->getConnection();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = sanitize($_POST['role']);

    // Validate garxa
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // email xa ki xaina check garna lai
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $error = "Email already exists!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $success = "Account created successfully! You can now login.";
            } else {
                $error = "Error creating account. Please try again.";
            }
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
    <title>Inventora - Sign Up</title>
    <link rel="stylesheet" href="./css/signUp.css">
    
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>Inventora</h1>
            <p>Create your account</p>
        </div>

        <form id="signupForm">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" required>
                <div class="error" id="nameError">Please enter your full name</div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" required>
                <div class="error" id="emailError">Please enter a valid email</div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" required>
                <div class="password-strength">
                    <div class="strength-bar" id="strengthBar"></div>
                </div>
                <div class="error" id="passwordError">Password must be at least 6 characters</div>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" required>
                <div class="error" id="confirmError">Passwords don't match</div>
            </div>

            <div class="form-group">
                <label>Select Role</label>
                <div class="role-options">
                    <button type="button" class="role-btn selected" data-role="user">User</button>
                    <button type="button" class="role-btn" data-role="admin">Admin</button>
                </div>
                <input type="hidden" id="selectedRole" value="user">
            </div>

            <button type="submit" class="submit-btn">Create Account</button>

            <div class="success" id="successMsg">Account created! Redirecting...</div>

            <div class="login-link">
                Already have an account? <a href="login.php">Sign In</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Role selection
            const roleBtns = document.querySelectorAll('.role-btn');
            const selectedRole = document.getElementById('selectedRole');
            
            roleBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    roleBtns.forEach(b => b.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedRole.value = this.dataset.role;
                });
            });

            // Password strength
            const passwordInput = document.getElementById('password');
            const strengthBar = document.getElementById('strengthBar');
            
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                if (password.length >= 6) strength = 33;
                if (password.length >= 8) strength = 66;
                if (password.length >= 10 && /[A-Z]/.test(password) && /[0-9]/.test(password)) strength = 100;
                
                strengthBar.style.width = strength + '%';
                strengthBar.style.background = strength < 33 ? '#e74c3c' : strength < 66 ? '#f39c12' : '#27ae60';
            });

            // Form validation and submission
            const form = document.getElementById('signupForm');
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Reset errors
                document.querySelectorAll('.error').forEach(el => el.style.display = 'none');
                document.getElementById('successMsg').style.display = 'none';
                
                // Get form values
                const name = document.getElementById('fullName').value.trim();
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                const role = selectedRole.value;
                
                let isValid = true;
                
                // Validate name
                if (name.length < 2) {
                    document.getElementById('nameError').style.display = 'block';
                    isValid = false;
                }
                
                // Validate email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    document.getElementById('emailError').style.display = 'block';
                    isValid = false;
                }
                
                // Validate password
                if (password.length < 6) {
                    document.getElementById('passwordError').style.display = 'block';
                    isValid = false;
                }
                
                // Validate confirm password
                if (password !== confirmPassword) {
                    document.getElementById('confirmError').style.display = 'block';
                    isValid = false;
                }
                
                if (isValid) {
                    // Store user data (in production, this would go to a backend)
                    const userData = {
                        name: name,
                        email: email,
                        password: password, 
                        role: role,
                        createdAt: new Date().toISOString()
                    };
                    
                    // Save to localStorage for demo
                    localStorage.setItem('user_' + email, JSON.stringify(userData));
                    
                    // Save to users list
                    const users = JSON.parse(localStorage.getItem('users') || '[]');
                    users.push({email: email, password: password, role: role, name: name});
                    localStorage.setItem('users', JSON.stringify(users));
                    
                    // Show success message
                    document.getElementById('successMsg').style.display = 'block';
                    
                    // Auto-fill email for login
                    localStorage.setItem('lastSignedUpEmail', email);
                    
                    // Redirect to login after 2 seconds
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                }
            });
        });
    </script>
</body>
</html>