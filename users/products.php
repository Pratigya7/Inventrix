<?php
require_once '../admin/auth.php';
include 'sidebar.html';
?>

<link rel="stylesheet" href="./css/product.css">

<div class="main-content">
    <h2>Available Products</h2>
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
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="productTable"></tbody>
        </table>
    </div>
</div>

<!-- Checkout Modal -->
<div id="checkoutModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeCheckoutModal()">&times;</span>
        <h2>Checkout Product</h2>
        <div class="form-group">
            <label>Product: <span id="checkoutProductName"></span></label>
        </div>
        <div class="form-group">
            <label>Unit Price: <span id="checkoutUnitPrice"></span></label>
        </div>
        <div class="form-group">
            <label>Available Stock: <span id="checkoutStock"></span></label>
        </div>
        <div class="form-group">
            <label>Quantity to Checkout:</label>
            <input type="number" id="checkoutQuantity" min="1" required>
        </div>
        <div class="form-group">
            <label>Total Price: <span id="checkoutTotalPrice">0.00</span></label>
        </div>
        <button class="btn" onclick="confirmCheckout()">Confirm Checkout</button>
    </div>
</div>

<div id="toast-container"></div>

<script>
let selectedProduct = null;
function closeCheckoutModal() {
    document.getElementById('checkoutModal').style.display = 'none';
}


document.addEventListener('DOMContentLoaded', () => {
    fetch('../admin/fetch_product.php')
    .then(res => res.json())
    .then(products => {
        const table = document.getElementById('productTable');
        table.innerHTML = '';
        products.forEach(p => {
            const totalPrice = (p.stock * p.unit_price).toFixed(2);
            table.innerHTML += `
                <tr id="productRow${p.id}">
                    <td>${p.product_name}</td>
                    <td>${parseFloat(p.unit_price).toFixed(2)}</td>
                    <td>${p.category}</td>
                    <td id="stockCell${p.id}">${p.stock}</td>
                    <td id="totalPriceCell${p.id}">${totalPrice}</td>
                    <td>${p.supplier}</td>
                    <td>
                        <button onclick="openCheckoutModal(${p.id}, '${p.product_name}', ${p.unit_price}, ${p.stock})">Checkout</button>
                    </td>
                </tr>
            `;
        });
    });
});


// Open checkout modal
function openCheckoutModal(id, name, unitPrice, stock) {
    selectedProduct = { id, name, unitPrice, stock };
    document.getElementById('checkoutProductName').innerText = name;
    document.getElementById('checkoutUnitPrice').innerText = unitPrice.toFixed(2);
    document.getElementById('checkoutStock').innerText = stock;
    document.getElementById('checkoutQuantity').value = 1;
    document.getElementById('checkoutTotalPrice').innerText = unitPrice.toFixed(2);

    document.getElementById('checkoutQuantity').addEventListener('input', updateCheckoutTotalPrice);
    document.getElementById('checkoutModal').style.display = 'flex';
}

// total price dynamically update garchha according to the stock
function updateCheckoutTotalPrice() {
    let qty = Number(document.getElementById('checkoutQuantity').value || 0);
    if(qty > selectedProduct.stock){
        qty = selectedProduct.stock;
        document.getElementById('checkoutQuantity').value = qty;
        showToast("Requested quantity exceeds available stock!", "error");
    }
    document.getElementById('checkoutTotalPrice').innerText = (qty * selectedProduct.unitPrice).toFixed(2);
}


function confirmCheckout() {
    let qty = Number(document.getElementById('checkoutQuantity').value);
    if (!qty || qty <= 0) return;

    if (qty > selectedProduct.stock) {
        showToast("Quantity exceeds available stock!", "error");
        return;
    }

    // Make AJAX call to update stock in database
    const formData = new FormData();
    formData.append('productId', selectedProduct.id);
    formData.append('quantity', qty);

    fetch('checkout_product.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
          
            selectedProduct.stock = data.newStock;
            document.getElementById(`stockCell${selectedProduct.id}`).innerText = selectedProduct.stock;
            document.getElementById(`totalPriceCell${selectedProduct.id}`).innerText = (selectedProduct.stock * selectedProduct.unitPrice).toFixed(2);

            showToast(`Checked out ${qty} x ${selectedProduct.name}`, 'success');
            closeCheckoutModal();

           
            localStorage.setItem('dashboardRefresh', Date.now());
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('An error occurred', 'error');
    });
}



function showToast(message, type="success"){
    const container = document.getElementById("toast-container");
    const toast = document.createElement("div");
    toast.className = `toast toast-${type}`;
    toast.innerText = message;
    container.appendChild(toast);
    setTimeout(()=> container.removeChild(toast), 4000);
}

</script>


