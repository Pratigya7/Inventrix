

<?php
require_once 'auth.php';
$pageTitle = "Analytics Overview";
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inventora – Dashboard</title>

<!-- Main CSS -->
<link rel="stylesheet" href="./css/style.css">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title>Inventora – <?= $pageTitle ?></title>
</head>

<body>

<!-- Sidebar -->
<div id="sidebar-container"></div>

<!-- Main Content -->
<div class="main-content">

    <!-- Header -->
   <?php include 'header.php'; ?>
   

    <!-- Analytics -->
    <div class="analytics-overview">

        <div class="analytics-cards">
            <div class="analytics-card">
                <div class="card-title">Products by Category</div>
                <canvas id="categoryChart"></canvas>
            </div>

            <div class="analytics-card">
                <div class="card-title">Products by Supplier</div>
                <canvas id="supplierChart"></canvas>
            </div>
        </div>

        <div class="analytics-card">
            <div class="card-title">Stock Levels Overview</div>
            <canvas id="stockChart"></canvas>
        </div>

    </div>

    <div class="footer">
        © 2025 Inventora. All rights reserved.
    </div>
</div>

<script>

fetch('./sidebar.html')
.then(res => res.text())
.then(data => {
    document.getElementById('sidebar-container').innerHTML = data;

    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-item a').forEach(link => {
        const linkPage = link.getAttribute('href').split('/').pop();
        if (linkPage === currentPage) link.classList.add('active');
    });
});


fetch('fetch_product.php')
.then(res => res.json())
.then(products => {

    /* Category Chart */
    const categories = {};
    const suppliers = {};
    let low = 0, medium = 0, high = 0;

    products.forEach(p => {
        categories[p.category] = (categories[p.category] || 0) + 1;
        suppliers[p.supplier] = (suppliers[p.supplier] || 0) + 1;

        if (p.stock < 30) low++;
        else if (p.stock < 100) medium++;
        else high++;
    });

    new Chart(categoryChart, {
        type: 'doughnut',
        data: {
            labels: Object.keys(categories),
            datasets: [{
                data: Object.values(categories),
                backgroundColor: ['#3498db','#2ecc71','#f39c12','#9b59b6','#e74c3c']
            }]
        },
        options: { plugins:{ legend:{ position:'bottom' }}}
    });

    new Chart(supplierChart, {
        type: 'bar',
        data: {
            labels: Object.keys(suppliers),
            datasets: [{
                label: 'Products',
                data: Object.values(suppliers),
                backgroundColor: '#3498db'
            }]
        },
        options: { scales:{ y:{ beginAtZero:true }}}
    });

    new Chart(stockChart, {
        type: 'pie',
        data: {
            labels: ['Low Stock', 'Medium Stock', 'High Stock'],
            datasets: [{
                data: [low, medium, high],
                backgroundColor: ['#e74c3c','#f39c12','#2ecc71']
            }]
        }
    });
});
</script>

</body>
</html>
