<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UserHub - Dashboard</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo">
            <h1><i class="fas fa-cube"></i> UserHub</h1>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="user-details">
                <span class="user-name" id="userName">Jane Doe</span>
                <span class="user-email" id="userEmail">jane.doe@example.com</span>
            </div>
            <button class="logout-btn" onclick="logout()">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </div>
    </header>

    <!-- Main Layout -->
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <nav>
                <a href="index.html" class="nav-link active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="./products.html" class="nav-link">
                    <i class="fas fa-search"></i> Discover Products
                </a>
                <a href="./profilePage.html" class="nav-link">
                    <i class="fas fa-user"></i> My Profile
                </a>
                <a href="bill.html" class="nav-link">
                    <i class="fas fa-receipt"></i> My Bills
                    <span class="badge" id="pendingBills">2</span>
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <div class="quick-stats">
                    <h4>Quick Stats</h4>
                    <div class="stat-item">
                        <span>Products Bought</span>
                        <span class="stat-value">15</span>
                    </div>
                    <div class="stat-item">
                        <span>Total Spent</span>
                        <span class="stat-value">$1,250.00</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1>Dashboard Overview</h1>
                <p>Welcome back! Here's your billing and product summary.</p>
            </div>

            <!-- Billing Overview -->
            <div class="dashboard-section">
                <h2><i class="fas fa-file-invoice-dollar"></i> Your Bills At A Glance</h2>
                <p class="section-subtitle">Manage your billing and payments efficiently</p>
                
                <div class="billing-cards">
                    <div class="billing-card pending">
                        <h3>Pending Bills</h3>
                        <div class="billing-amount">
                            <span class="amount">$1,250.00</span>
                            <span class="count">2 bills</span>
                        </div>
                        <a href="bill.html" class="view-btn">View Details</a>
                    </div>
                    
                    <div class="billing-card paid">
                        <h3>Paid Bills</h3>
                        <div class="billing-amount">
                            <span class="amount">$3,450.75</span>
                            <span class="count">8 bills</span>
                        </div>
                        <a href="#" class="view-btn">View History</a>
                    </div>
                </div>
            </div>

            <!-- Products Available -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-box"></i> Products Available</h2>
                    <a href="products.html" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <!-- Category Tabs -->
                <div class="category-tabs">
                    <button class="tab-btn active" onclick="filterProducts('all')">All Products</button>
                    <button class="tab-btn" onclick="filterProducts('electronics')">Electronics</button>
                    <button class="tab-btn" onclick="filterProducts('home')">Home Goods</button>
                    <button class="tab-btn" onclick="filterProducts('apparel')">Apparel</button>
                    <button class="tab-btn" onclick="filterProducts('books')">Books</button>
                </div>

                <!-- Products Grid -->
                <div class="products-grid" id="productsGrid">
                    <!-- Products will be loaded here -->
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2025 UserHub. All rights reserved.</p>
    </footer>

    <!-- JavaScript -->
    <script src="./main.js"></script>
    <script>
        // Load products on dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
            loadUserInfo();
        });
    </script>
</body>
</html>