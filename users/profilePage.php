<?php require_once '../admin/auth.php'; ?>
<?php include 'sidebar.html'; ?>

<link rel="stylesheet" href="./css/profile.css">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="main-content">
    <form id="profileForm">
        <h2>Edit Profile</h2>

        <label>Username</label>
        <input type="text" id="username" value="<?= $_SESSION['user_name'] ?>" required>

        <label>Email</label>
        <input type="email" id="email" value="<?= $_SESSION['user_email'] ?>" required>

        <!-- <label>Password</label>
        <div class="password-wrapper">
            <input type="password" id="password" placeholder="Enter new password">
            <i class="fa-solid fa-eye toggle-password" onclick="togglePassword(this)"></i>
        </div> -->

        <label>Password</label>
<div class="password-wrapper">
    <input type="password" id="password" placeholder="Enter new password">
    <i class="fa-solid fa-eye toggle-password" onclick="togglePassword(this)"></i>
</div>


        <button type="submit">Save Changes</button>
    </form>
</div>

<div id="toast-container"></div>

<script>
function togglePassword(icon) {
    const passField = document.getElementById('password');

    if (passField.type === 'password') {
        passField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function showToast(message, type = "success") {
    const container = document.getElementById("toast-container");
    const toast = document.createElement("div");
    toast.className = `toast toast-${type}`;
    toast.innerText = message;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('fade-out');
        setTimeout(() => toast.remove(), 500);
    }, 3500);
}

document.getElementById('profileForm').addEventListener('submit', e => {
    e.preventDefault();

    const payload = {
        username: document.getElementById('username').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value
    };

    fetch('api/update_profile.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        showToast(data.message, data.success ? "success" : "error");
        if (data.success) document.getElementById('password').value = '';
    })
    .catch(() => showToast("Error updating profile", "error"));
});
</script>
