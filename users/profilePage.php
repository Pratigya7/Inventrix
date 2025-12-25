<?php require_once '../admin/auth.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <h2>Edit Profile</h2>
    <form id="profileForm">
        <label>Name</label><input type="text" id="name" value="<?= $_SESSION['username'] ?>">
        <label>Email</label><input type="email" id="email" value="<?= $_SESSION['email'] ?>">
        <label>Phone</label><input type="text" id="phone" value="<?= $_SESSION['phone'] ?>">
        <button type="submit">Save Changes</button>
    </form>
</div>

<div id="toast-container"></div>

<script>
document.getElementById('profileForm').addEventListener('submit', e => {
    e.preventDefault();
    fetch('api/update_profile.php', {
        method: 'POST',
        headers: { 'Content-Type':'application/json' },
        body: JSON.stringify({
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value
        })
    })
    .then(res => res.json())
    .then(data => alert(data.message));
});
</script>
