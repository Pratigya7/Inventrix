<?php require_once 'auth.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventora - Products</title>
    <link rel="stylesheet" href="./css/product.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar-container"></div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <div class="page-title"><h1>Product List</h1></div>
            <div class="user-info"><div class="user-avatar">PP</div><span>Admin</span></div>
        </div>

        <div class="page-content">
            <div class="content-header">
                <div class="content-title">
                    <h2>All Products</h2>
                    <p>Manage your inventory products, including details, stock levels, unit price, and suppliers.</p>
                </div>
                <button class="btn" id="addProductBtn">Add New Product</button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Unit Price</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Total Price</th>
                            <th>Supplier</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productTable"></tbody>
                </table>
            </div>
        </div>

        <div class="footer">
            <p>© 2025 Inventora. All rights reserved.</p>
        </div>
    </div>

    <!-- Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add Product</h2>
            <form id="productForm">
                <input type="hidden" id="productId">

                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" id="productName" required>
                </div>

                <div class="form-group">
                    <label>Unit Price</label>
                    <input type="number" step="0.01" id="unitPrice" required>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <input type="text" id="category" required>
                </div>

                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" id="stock" required>
                </div>

                <div class="form-group">
                    <label>Supplier</label>
                    <select id="supplier" required>
                        <option value="">Select Supplier</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Total Price: <span id="totalPrice">0.00</span></label>
                </div>

                <button type="submit" class="btn">Save Product</button>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeDeleteModal()">&times;</span>
            <h2>Confirm Delete</h2>
            <p>Are you sure you want to delete this product?</p>
            <div class="modal-buttons">
                <button class="btn" onclick="confirmDelete()">Yes, Delete</button>
                <button class="btn cancel-btn" onclick="closeDeleteModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Load sidebar
    fetch('./sidebar.html')
        .then(res => res.text())
        .then(data => {
            document.getElementById('sidebar-container').innerHTML = data;
            const currentPage = window.location.pathname.split('/').pop();
            document.querySelectorAll('.nav-item a').forEach(link => {
                link.classList.toggle('active', link.getAttribute('href').split('/').pop() === currentPage);
            });
        });

    // Fetch products
    fetchProducts();

    // Add product button
    document.getElementById("addProductBtn").addEventListener("click", () => {
        document.getElementById("modalTitle").innerText = "Add Product";
        document.getElementById("productForm").reset();
        document.getElementById("productId").value = "";
        loadSuppliers();
        updateTotalPrice();
        openModal();
    });

    // Form submit
    document.getElementById("productForm").addEventListener("submit", e => {
        e.preventDefault();
        const id = document.getElementById("productId").value;
        const payload = {
            id,
            product_name: document.getElementById("productName").value,
            unit_price: document.getElementById("unitPrice").value,
            category: document.getElementById("category").value,
            stock: document.getElementById("stock").value,
            supplier: document.getElementById("supplier").value
        };
        const url = id ? "update_product.php" : "add_product.php";
        fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) { showToast(data.message || "Failed to save product", "error"); return; }
            closeModal();
            fetchProducts();
            showToast("Product saved successfully!", "success");
        })
        .catch(err => showToast("Error: " + err.message, "error"));
    });

    // Calculate total price
    ["stock","unitPrice"].forEach(id => {
        document.getElementById(id).addEventListener("input", updateTotalPrice);
    });
});

function fetchProducts() {
    fetch('fetch_product.php')
    .then(res => res.json())
    .then(data => {
        const table = document.getElementById('productTable');
        table.innerHTML = '';
        data.forEach(product => {
            const totalPrice = (product.stock * product.unit_price).toFixed(2);
            table.innerHTML += `
                <tr>
                    <td>${product.product_name}</td>
                    <td>${parseFloat(product.unit_price).toFixed(2)}</td>
                    <td>${product.category}</td>
                    <td>${product.stock}</td>
                    <td>${totalPrice}</td>
                    <td>${product.supplier}</td>
                    <td class="action-buttons">
                        <button class="action-btn edit-btn" onclick="editProduct(${product.id})"><i class="fa-regular fa-pen-to-square"></i></button>
                        <button class="action-btn delete-btn" onclick="deleteProduct(${product.id})"><i class="fa-regular fa-trash-can"></i></button>
                    </td>
                </tr>
            `;
        });
    });
}

function editProduct(id) {
    fetch(`get_product.php?id=${id}`)
    .then(res => res.json())
    .then(product => {
        document.getElementById("modalTitle").innerText = "Edit Product";
        document.getElementById("productId").value = product.id;
        document.getElementById("productName").value = product.product_name;
        document.getElementById("unitPrice").value = product.unit_price;
        document.getElementById("category").value = product.category;
        document.getElementById("stock").value = product.stock;
        loadSuppliers(product.supplier);
        updateTotalPrice();
        openModal();
    });
}

let deleteId = null;
function deleteProduct(id){ deleteId = id; openDeleteModal(); }
function openDeleteModal(){ document.getElementById("deleteModal").style.display="flex"; }
function closeDeleteModal(){ document.getElementById("deleteModal").style.display="none"; }
function confirmDelete(){
    fetch('delete_product.php',{
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body:JSON.stringify({id:deleteId})
    })
    .then(res=>res.json())
    .then(data=>{
        if(!data.success){ showToast(data.message || "Failed to delete", "error"); return; }
        fetchProducts();
        closeDeleteModal();
        showToast("Product deleted successfully!", "success");
    });
}

function openModal(){ document.getElementById("productModal").style.display="flex"; }
function closeModal(){ document.getElementById("productModal").style.display="none"; }
function showToast(msg,type="success"){
    const c = document.getElementById("toast-container");
    const t = document.createElement("div");
    t.className=`toast toast-${type}`;
    t.innerText = msg;
    c.appendChild(t);
    setTimeout(()=>c.removeChild(t),4000);
}

function loadSuppliers(selected=""){
    fetch("fetch_supplier.php")
    .then(res=>res.json())
    .then(data=>{
        const sel = document.getElementById("supplier");
        sel.innerHTML=`<option value="">Select Supplier</option>`;
        data.forEach(s=>{
            sel.innerHTML += `<option value="${s.name}" ${s.name===selected?"selected":""}>${s.name}</option>`;
        });
    });
}

function updateTotalPrice(){
    const stock = Number(document.getElementById("stock").value || 0);
    const price = Number(document.getElementById("unitPrice").value || 0);
    document.getElementById("totalPrice").innerText=(stock*price).toFixed(2);
}
</script>

</body>
</html>
