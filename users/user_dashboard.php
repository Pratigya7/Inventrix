<?php 
require_once '../admin/auth.php'; 
include 'sidebar.html'; 
?>

<link rel="stylesheet" href="./css/user.css">



<div class="main-content">
    <h1>Welcome, <?= $_SESSION['user_name'] ?? 'User' ?> </h1>
    <p>Here is your overview of your account and products.</p>

    <div class="card-container">
        <div class="card">
            <h2>Total Products</h2>
            <p id="totalProducts">Loading...</p>
        </div>

        <div class="card">
            <h2>My Orders</h2>
            <p id="myOrders">0</p>
        </div>

        <div class="card">
            <h2>Profile Completeness</h2>
            <p id="profileCompletion">85%</p>
        </div>

        <div class="card">
            <h2>Last Login</h2>
            <p><?= $_SESSION['last_login'] ?? 'N/A' ?></p>
        </div>
    </div>
</div>

<script>
// Fetch total products added by ADmin
fetch('../admin/fetch_product.php')
    .then(res => res.json())
    .then(data => {
        document.getElementById('totalProducts').innerText = data.length;
    })
    .catch(err => {
        console.error('Failed to fetch products:', err);
        document.getElementById('totalProducts').innerText = 'Error';
    });


</script>

