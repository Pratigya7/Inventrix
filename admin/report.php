<?php
require_once 'auth.php';
$pageTitle = "Products Report";
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <link rel="stylesheet" href="./css/report.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Inventrix – <?= $pageTitle ?></title>
   
</head>
<body>
    <!-- Sidebar -->
    

    <div id="sidebar-container"></div>
    
    <!-- Main Content -->
    <div class="main-content">
        <?php include 'header.php'; ?>
        
        <div class="page-content">
            <div class="content-header">
                <div class="content-title">
                    <h2>Filter Reports</h2>
                </div>
            </div>
            
            <div class="filter-section">
                <h3 class="filter-title">Report Filters</h3>
                <form id="reportFilterForm">
                    <div class="filter-form">
                        <div class="form-group">
                            <label for="dateRange">Date Range</label>
                            <input type="text" id="dateRange" value="Jan 01, 2023 - Dec 31, 2023" readonly>
                        </div>
                        <div class="form-group">
                            <label for="supplier">Supplier</label>
                            <select id="supplier">
                                <option>All Suppliers</option>
                                <option>Global Supplies Inc.</option>
                                <option>Tech Solutions Ltd.</option>
                                <option>Office Essentials Co.</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="product">Product</label>
                            <select id="product">
                                <option>All Products</option>
                                <option>Laptop Pro X</option>
                                <option>Wireless Mouse Z</option>
                                <option>Ergonomic Keyboard</option>
                            </select>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button type="submit" class="btn">Generate Report</button>
                    </div>
                </form>
            </div>
            
            <div class="content-header">
                <div class="content-title">
                    <h2>Report Preview</h2>
                </div>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                          
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Price</th>
                            <th>ALert</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                          
                            <td>2023-01-15</td>
                            <td>Global Supplies Inc.</td>
                            <td>Laptop Pro X</td>
                            <td>10</td>
                            <td>Rs. 1200.00</td>
                            <td>Rs. 12000.00</td>
                            <td class="status-completed">High</td>
                        </tr>
                     
                   
                        <tr>
                           
                            <td>2023-02-10</td>
                            <td>Global Supplies Inc.</td>
                            <td>Monitor UltraView</td>
                            <td>5</td>
                            <td>Rs. 350.00</td>
                            <td>Rs. 1750.00</td>
                            <td class="status-cancelled">Low</td>
                        </tr>
                        <tr>
                           
                            <td>2023-02-15</td>
                            <td>Tech Solutions Ltd.</td>
                            <td>USB-C Hub</td>
                            <td>100</td>
                            <td>Rs. 15.00</td>
                            <td>Rs. 1500.00</td>
                            <td class="status-completed">High</td>
                        </tr>
                    
                    </tbody>
                </table>
            </div>
            
            <div class="pagination">
                <div class="pagination-info">Showing 1-6 of 20 results</div>
                <div class="pagination-controls">
                    <button class="page-btn">Previous</button>
                    <button class="page-btn active">1</button>
                    <button class="page-btn">2</button>
                    <button class="page-btn">3</button>
                    <button class="page-btn">Next</button>
                </div>
            </div>
            
            <div class="export-options">
                <button class="export-btn pdf"> Export as PDF</button>
                <button class="export-btn excel"> Export as Excel</button>
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
                    
                    const pageName = this.textContent.trim();
                    document.querySelector('.page-title h1').textContent = pageName;
                });
            });
            
            // Report filter form
            const reportFilterForm = document.getElementById('reportFilterForm');
            reportFilterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const filters = {
                    dateRange: document.getElementById('dateRange').value,
                    supplier: document.getElementById('supplier').value,
                    product: document.getElementById('product').value
                };
                
                console.log('Report filters:', filters);
                alert('Report generated with selected filters!');
            });
            
            // Export buttons
            const exportButtons = document.querySelectorAll('.export-btn');
            exportButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const format = this.classList.contains('pdf') ? 'PDF' : 'Excel';
                    alert(`Exporting report as ${format}`);
                });
            });
        });

        
document.addEventListener('DOMContentLoaded', function() {
    // Load sidebar.html
fetch('./sidebar.html')
    .then(response => response.text())
    .then(data => {
        document.getElementById('sidebar-container').innerHTML = data;

        const currentPage = window.location.pathname.split('/').pop();

        document.querySelectorAll('.nav-item a').forEach(link => {
            const linkPage = link.getAttribute('href').split('/').pop();

            if (linkPage === currentPage) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    })
    .catch(err => console.error('Failed to load sidebar:', err));

});


    </script>
</body>
</html>