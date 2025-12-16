<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventora - Dashboard</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h1>Inventora</h1>
        </div>
        
        <div class="nav-menu">
            <div class="nav-item active">
                <a href="./index.html"><i>📊Dashboard</i> </a>
            </div>
            <div class="nav-item">
                <a href="./report.html"><i>📋</i> Report</a>
            </div>
            <div class="nav-item">
                <a href="./product.html"><i>📦</i> Product</a>
            </div>
            <div class="nav-item">
               <a href="./supplier.html"> <i>🏢</i> Supplier</a>
            </div>
            <div class="nav-item">
                <a href="./purchaseOrder.html"><i>📝</i> Purchase Order</a>
            </div>
            <div class="nav-item">
                <a href="./User.html"><i>👥</i> User</a>
            </div>
        </div>

    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <div class="page-title">
                <h1>Analytics Overview</h1>
            </div>
            <div class="user-info">
                <div class="user-avatar">PP</div>
                <span>Pratigya</span>
            </div>
        </div>
        
        <div class="analytics-overview">
            <div class="analytics-cards">
                <div class="analytics-card">
                    <div class="card-title">Purchase by Order</div>
                    <p>Distribution of purchase orders by product category.</p>
                    <div class="chart-placeholder">
                        Chart: Electronics, Apparel, Books
                    </div>
                </div>
                
                <div class="analytics-card">
                    <div class="card-title">Product Assignment to Supplier</div>
                    <p>Number of products managed by each key supplier.</p>
                    <div class="chart-placeholder">
                        Chart: TechCorp, FashionWarner, etc.
                    </div>
                </div>
            </div>
            
            <div class="analytics-card">
                <div class="card-title">Delivery History per Day</div>
                <p>Daily count of completed product deliveries over the last week.</p>
                
                <div class="delivery-chart">
                    <div class="delivery-bar" style="height: 80%;">
                        <div class="delivery-label">Mon</div>
                    </div>
                    <div class="delivery-bar" style="height: 60%;">
                        <div class="delivery-label">Tue</div>
                    </div>
                    <div class="delivery-bar" style="height: 90%;">
                        <div class="delivery-label">Wed</div>
                    </div>
                    <div class="delivery-bar" style="height: 70%;">
                        <div class="delivery-label">Thu</div>
                    </div>
                    <div class="delivery-bar" style="height: 50%;">
                        <div class="delivery-label">Fri</div>
                    </div>
                    <div class="delivery-bar" style="height: 40%;">
                        <div class="delivery-label">Sat</div>
                    </div>
                    <div class="delivery-bar" style="height: 30%;">
                        <div class="delivery-label">Sun</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>© 2025 Inventora. All rights reserved.</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navigation
            const navItems = document.querySelectorAll('.nav-item');
            
            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    navItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    
                    // In a real app, you would load the corresponding page content
                    const pageName = this.textContent.trim();
                    document.querySelector('.page-title h1').textContent = pageName;
                });
            });
        });
    </script>
</body>
</html>