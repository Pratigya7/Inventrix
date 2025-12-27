<?php
require_once 'auth.php';
$pageTitle = "Analytics Overview";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inentrix – <?= $pageTitle ?></title>

<link rel="stylesheet" href="./css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <div class="card-title">Stock by Category</div>
                <canvas id="categoryChart"></canvas>
            </div>

            <div class="analytics-card">
                <div class="card-title">Products by Supplier</div>
                <canvas id="supplierChart"></canvas>
            </div>
        </div>

        <div class="analytics-card">
            <div class="card-title">Stock Levels (Per Product)</div>
            <canvas id="stockChart"></canvas>
        </div>

    </div>

    <div class="footer">
        © 2025 Inventora. All rights reserved.
    </div>
</div>

<script>
/* ---------------- SIDEBAR ---------------- */
fetch('./sidebar.html')
.then(res => res.text())
.then(html => {
    document.getElementById('sidebar-container').innerHTML = html;

    const current = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-item a').forEach(link => {
        link.classList.toggle(
            'active',
            link.getAttribute('href').split('/').pop() === current
        );
    });
});

/* ---------------- CHART INSTANCES ---------------- */
let categoryChartInstance;
let supplierChartInstance;
let stockChartInstance;

/* ---------------- LOAD DASHBOARD DATA ---------------- */
function loadDashboardCharts() {
    fetch('fetch_product.php')
    .then(res => res.json())
    .then(products => {

        const categoryStock = {};
        const supplierCount = {};

        products.forEach(p => {
            const stock = Number(p.stock || 0);
            if (isNaN(stock)) return;

            /* ---- Stock by Category ---- */
            categoryStock[p.category] = (categoryStock[p.category] || 0) + stock;

            /* ---- Products by Supplier ---- */
            supplierCount[p.supplier] = (supplierCount[p.supplier] || 0) + 1;
        });

        /* Destroy old charts if exist */
        categoryChartInstance?.destroy();
        supplierChartInstance?.destroy();
        stockChartInstance?.destroy();

        /* ---------------- CATEGORY CHART ---------------- */
        categoryChartInstance = new Chart(
            document.getElementById('categoryChart'),
            {
                type: 'doughnut',
                data: {
                    labels: Object.keys(categoryStock),
                    datasets: [{
                        data: Object.values(categoryStock),
                        backgroundColor: [
                            '#3498db',
                            '#2ecc71',
                            '#f39c12',
                            '#9b59b6',
                            '#e74c3c',
                            '#1abc9c'
                        ]
                    }]
                },
                options: {
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: ctx => `Stock: ${ctx.raw}`
                            }
                        }
                    }
                }
            }
        );

        /* ---------------- SUPPLIER CHART ---------------- */
        supplierChartInstance = new Chart(
            document.getElementById('supplierChart'),
            {
                type: 'bar',
                data: {
                    labels: Object.keys(supplierCount),
                    datasets: [{
                        label: 'Products',
                        data: Object.values(supplierCount),
                        backgroundColor: '#3498db'
                    }]
                },
                options: {
                    scales: { y: { beginAtZero: true } }
                }
            }
        );

        /* ---------------- STOCK LEVEL CHART (Per Product) ---------------- */
        const productLabels = products.map(p => p.product_name || 'Unnamed');
        const productStocks = products.map(p => Number(p.stock || 0));

        stockChartInstance = new Chart(
            document.getElementById('stockChart'),
            {
                type: 'bar',
                data: {
                    labels: productLabels,
                    datasets: [{
                        label: 'Stock Quantity',
                        data: productStocks,
                        backgroundColor: '#2ecc71'
                    }]
                },
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: ctx => `${ctx.label}: ${ctx.raw} units`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Stock Quantity' }
                        },
                        x: {
                            title: { display: true, text: 'Product' }
                        }
                    }
                }
            }
        );

    });
}

/* ---------------- INITIAL LOAD ---------------- */
loadDashboardCharts();

/* ---------------- AUTO REFRESH FROM PRODUCT PAGE ---------------- */
window.addEventListener("storage", e => {
    if (e.key === "dashboardRefresh") {
        loadDashboardCharts();
    }
});

/* ---------------- SAME TAB FALLBACK ---------------- */
setInterval(() => {
    if (localStorage.getItem("dashboardRefresh")) {
        loadDashboardCharts();
        localStorage.removeItem("dashboardRefresh");
    }
}, 2000);
</script>

</body>
</html>
