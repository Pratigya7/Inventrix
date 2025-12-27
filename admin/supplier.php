<?php
require_once 'auth.php';
$pageTitle = "Suppliers Details";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventora - Suppliers</title>

    <link rel="stylesheet" href="./css/supplier.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <title>Inventora – <?= $pageTitle ?></title>

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
                <h2>Current Suppliers</h2>
                <p>Manage your supplier information and contacts.</p>
            </div>
            <button class="btn" id="addSupplierBtn">Add New Supplier</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Supplier Name</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="supplierTable">
                    <!-- Dynamic rows -->
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        <p>© 2025 Inventora. All rights reserved.</p>
    </div>
</div>

<!-- Supplier Modal -->
<div class="modal" id="supplierModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add Supplier</h3>
            <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>

        <form id="supplierForm">
            <input type="hidden" id="supplierId">

            <div class="form-row">
                <div class="form-group">
                    <label>Supplier Name</label>
                    <input type="text" id="supplierName" required>
                </div>
                <div class="form-group">
                    <label>Contact Person</label>
                    <input type="text" id="contactPerson" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="email" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" id="phone" required>
                </div>
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" id="address" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn">Save Supplier</button>
            </div>
        </form>
    </div>
</div>

<script>

// fetch('./sidebar.html')
//     .then(res => res.text())
//     .then(data => {
//         document.getElementById('sidebar-container').innerHTML = data;
//         const currentPage = location.pathname.split('/').pop();
//         document.querySelectorAll('.nav-item a').forEach(link => {
//             if (link.getAttribute('href') === currentPage) {
//                 link.classList.add('active');
//             }
//         });
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

    

/* ================= CRUD LOGIC ================= */
let editingId = null;

document.addEventListener('DOMContentLoaded', () => {
    fetchSuppliers();

    document.getElementById('addSupplierBtn').addEventListener('click', () => {
        editingId = null;
        document.getElementById('modalTitle').innerText = "Add Supplier";
        document.getElementById('supplierForm').reset();
        openModal();
    });

    document.getElementById('supplierForm').addEventListener('submit', saveSupplier);
});

/* ================= FETCH ================= */
function fetchSuppliers() {
    fetch('fetch_supplier.php')
        .then(res => res.json())
        .then(data => {
            const table = document.getElementById('supplierTable');
            table.innerHTML = '';

            data.forEach(s => {
                table.innerHTML += `
                    <tr>
                        <td>${s.name}</td>
                        <td>${s.contact_person}</td>
                        <td>${s.email}</td>
                        <td>${s.phone}</td>
                        <td>${s.address}</td>
                        <td class="action-buttons">
                            <button class="action-btn edit-btn" onclick="editSupplier(${s.id})">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </button>
                            <button class="action-btn delete-btn" onclick="deleteSupplier(${s.id})">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        });
}

/* ================= ADD / UPDATE ================= */
function saveSupplier(e) {
    e.preventDefault();

    const payload = {
        id: editingId,
        name: supplierName.value,
        contact: contactPerson.value,
        email: email.value,
        phone: phone.value,
        address: address.value
    };

    const url = editingId
        ? 'update_supplier.php'
        : 'add_supplier.php';

    fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert("Operation failed");
            return;
        }
        closeModal();
        fetchSuppliers();
    });
}

/* ================= EDIT ================= */
function editSupplier(id) {
    fetch('fetch_supplier.php')
        .then(res => res.json())
        .then(data => {
            const s = data.find(x => x.id == id);

            editingId = s.id;
            modalTitle.innerText = "Edit Supplier";

            supplierName.value = s.name;
            contactPerson.value = s.contact_person;
            email.value = s.email;
            phone.value = s.phone;
            address.value = s.address;

            openModal();
        });
}

/* ================= DELETE ================= */
function deleteSupplier(id) {
    if (!confirm("Are you sure you want to delete this supplier?")) return;

    fetch('delete_supplier.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) fetchSuppliers();
        else alert("Delete failed");
    });
}

/* ================= MODAL ================= */
function openModal() {
    document.getElementById('supplierModal').style.display = "flex";
}

function closeModal() {
    document.getElementById('supplierModal').style.display = "none";
}
</script>

</body>
</html>
