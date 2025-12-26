<?php
require_once 'auth.php';
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventora - User Management</title>
    <link rel="stylesheet" href="./css/user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" 
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar-container"></div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <div class="page-title"><h1>User Management</h1></div>
            <div class="user-info">
                <div class="user-avatar">PP</div>
                <span>Pratigya</span>
            </div>
        </div>

        <div class="page-content">
            <div class="content-header">
                <div class="content-title"><h2>Manage Existing Users</h2></div>
            </div>

            <div class="user-list"></div>

            <!-- <div class="add-user-form">
                <h3 class="form-title">Add New User</h3>
                <form id="userForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="userName">Name</label>
                            <input type="text" id="userName" required>
                        </div>
                        <div class="form-group">
                            <label for="userEmail">Email</label>
                            <input type="email" id="userEmail" required>
                        </div>
                    </div> -->
                    <!-- <div class="form-row">
                        <div class="form-group">
                            <label for="userRole">Role</label>
                            <select id="userRole" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="userPassword">Password</label>
                            <input type="password" id="userPassword" required>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="reset" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn">Add User</button>
                    </div> -->

                    <div class="add-user-form">
    <h3 class="form-title">Add New User</h3>
    <form id="userForm">
        <div class="form-row">
            <div class="form-group">
                <label for="userName">Name</label>
                <input type="text" id="userName" required>
                <div class="form-error" id="errorName">Please enter the user's full name.</div>
            </div>
            <div class="form-group">
                <label for="userEmail">Email</label>
                <input type="email" id="userEmail" required>
                <div class="form-error" id="errorEmail">Please enter a valid email address.</div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="userRole">Role</label>
                <select id="userRole" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
                <div class="form-error" id="errorRole">Please select a role.</div>
            </div>
            <div class="form-group">
                <label for="userPassword">Password</label>
                <input type="password" id="userPassword" required>
                <div class="form-error" id="errorPassword">Password must be at least 6 characters.</div>
            </div>
        </div>
        <div class="form-actions">
            <button type="reset" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn">Add User</button>
        </div>
   

                </form>
            </div>
        </div>

        <div class="footer">
            <p>© 2025 Inventora. All rights reserved.</p>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal">
            <h2>Confirm Deletion</h2>
            <p id="deleteMessage"></p>
            <div class="modal-buttons">
                <button class="btn btn-cancel" id="cancelDelete">Cancel</button>
                <button class="btn btn-delete" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal-overlay" id="editModal">
        <div class="modal">
            <h2>Edit User</h2>
            <form id="editForm">
                <input type="hidden" id="editUserId">
                <label>Name</label>
                <input type="text" id="editUserName" required>
                <label>Email</label>
                <input type="email" id="editUserEmail" required>
                <label>Role</label>
                <select id="editUserRole" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
                <div class="modal-buttons">
                    <button type="button" class="btn btn-cancel" id="cancelEdit">Cancel</button>
                    <button type="submit" class="btn btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>

        function showToast(message, type = "success") {
    const container = document.getElementById("toast-container");
    const toast = document.createElement("div");
    toast.className = `toast ${type}`;
    toast.textContent = message;
    container.appendChild(toast);

    // Show the toast
    setTimeout(() => toast.classList.add("show"), 50);

    // Hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}

document.addEventListener('DOMContentLoaded', () => {

    // -------- Sidebar Load --------
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


    // -------- User Management --------
    const userList = document.querySelector(".user-list");
    const userForm = document.getElementById("userForm");
    const userName = document.getElementById("userName");
    const userEmail = document.getElementById("userEmail");
    const userRole = document.getElementById("userRole");
    const userPassword = document.getElementById("userPassword");

    const deleteModal = document.getElementById("deleteModal");
    const deleteMessage = document.getElementById("deleteMessage");
    const cancelDelete = document.getElementById("cancelDelete");
    const confirmDelete = document.getElementById("confirmDelete");
    let deleteUserId = null;

    const editModal = document.getElementById("editModal");
    const editForm = document.getElementById("editForm");
    const editUserId = document.getElementById("editUserId");
    const editUserName = document.getElementById("editUserName");
    const editUserEmail = document.getElementById("editUserEmail");
    const editUserRole = document.getElementById("editUserRole");

    // -------- Render Single User --------
    function renderUser(user) {
        const roleClass = `role-${user.role}`;
        const initials = user.full_name.split(" ").map(n => n[0]).join("").toUpperCase();

        const div = document.createElement("div");
        div.className = "user-card";
        div.dataset.userId = user.id;

        div.innerHTML = `
            <div class="user-info-card">
                <div class="user-avatar-small">${initials}</div>
                <div class="user-details">
                    <h4>${user.full_name}</h4>
                    <p>${user.email}</p>
                </div>
            </div>
            <div class="user-role ${roleClass}">${user.role.charAt(0).toUpperCase() + user.role.slice(1)}</div>
            <div class="user-actions">
                <button class="action-btn edit-btn"><i class="fa-regular fa-pen-to-square"></i></button>
                <button class="action-btn delete-btn"><i class="fa-regular fa-trash-can"></i></button>
            </div>
        `;

        // Edit button
        div.querySelector(".edit-btn").addEventListener("click", () => {
            editUserId.value = user.id;
            editUserName.value = user.full_name;
            editUserEmail.value = user.email;
            editUserRole.value = user.role;
            editModal.style.display = "flex";
        });

        // Delete button
        div.querySelector(".delete-btn").addEventListener("click", () => {
            deleteUserId = user.id;
            deleteMessage.textContent = `Are you sure you want to delete ${user.full_name}?`;
            deleteModal.style.display = "flex";
        });

        userList.prepend(div);
    }

    // -------- Load Users from Server --------
    async function loadUsers() {
        try {
            const res = await fetch("get_user.php");
            const users = await res.json();
            userList.innerHTML = "";
            users.forEach(renderUser);
        } catch (err) {
            console.error("Failed to load users:", err);
        }
    }

    loadUsers();

    // -------- Real-time Validation --------
    const fields = [
        { input: userName, errorId: "errorName", validate: val => val.trim() !== "" },
        { input: userEmail, errorId: "errorEmail", validate: val => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val.trim()) },
        { input: userRole, errorId: "errorRole", validate: val => val !== "" },
        { input: userPassword, errorId: "errorPassword", validate: val => val.length >= 6 }
    ];

    fields.forEach(f => {
        f.input.addEventListener("input", () => {
            if (f.validate(f.input.value)) {
                f.input.classList.remove("invalid");
                f.input.classList.add("valid");
                document.getElementById(f.errorId).style.display = "none";
            } else {
                f.input.classList.remove("valid");
                f.input.classList.add("invalid");
                document.getElementById(f.errorId).style.display = "block";
            }
        });
    });

    // -------- Form Submission --------
    userForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        let valid = true;

        // Validate all fields before submission
        fields.forEach(f => {
            if (!f.validate(f.input.value)) {
                valid = false;
                f.input.classList.add("invalid");
                document.getElementById(f.errorId).style.display = "block";
            } else {
                f.input.classList.remove("invalid");
                f.input.classList.add("valid");
                document.getElementById(f.errorId).style.display = "none";
            }
        });

        if (!valid) return;

        // Submit to server
        const payload = {
            full_name: userName.value.trim(),
            email: userEmail.value.trim(),
            role: userRole.value,
            password: userPassword.value
        };

        try {
            const res = await fetch("create_user.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            // if (data.error) {
            //     alert(data.error);
            // } else {
            //     alert(data.success);
            //     renderUser(data.user);
            //     userForm.reset();
            //     fields.forEach(f => f.input.classList.remove("valid"));
            // }

            if (data.error) {
    showToast(data.error, "error");
} else {
    showToast(data.success, "success");
    renderUser(data.user);
    userForm.reset();
    fields.forEach(f => f.input.classList.remove("valid"));
}

        } catch (err) {
            console.error(err);
            alert("Something went wrong. Please try again.");
        }
    });

    // -------- Delete User --------
    cancelDelete.addEventListener("click", () => deleteModal.style.display = "none");

    confirmDelete.addEventListener("click", async () => {
        try {
            const res = await fetch(`delete_user.php?id=${deleteUserId}`, { method: "DELETE" });
            const data = await res.json();
            // if (data.success) {
            //     document.querySelector(`.user-card[data-user-id='${deleteUserId}']`).remove();
            //     alert(data.success);
            // } else alert(data.error);
            // deleteModal.style.display = "none";

            if (data.success) {
    document.querySelector(`.user-card[data-user-id='${deleteUserId}']`).remove();
    showToast(data.success, "success");
} else {
    showToast(data.error, "error");
    deleteModal.style.display = "none";
}

        } catch (err) {
            console.error(err);
            alert("Server error");
            deleteModal.style.display = "none";
        }
    });

    // -------- Edit User --------
    document.getElementById("cancelEdit").addEventListener("click", () => editModal.style.display = "none");

    editForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const payload = {
            full_name: editUserName.value.trim(),
            email: editUserEmail.value.trim(),
            role: editUserRole.value
        };
        try {
            const res = await fetch(`edit_user.php?id=${editUserId.value}`, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            // if (data.error) alert(data.error);
            // else {
            //     alert(data.success);
            //     loadUsers();
            //     editModal.style.display = "none";
            // }

            if (data.error) showToast(data.error, "error");
else {
    showToast(data.success, "success");
    loadUsers();
    editModal.style.display = "none";
}

        } catch (err) {
            console.error(err);
            alert("Server error");
            editModal.style.display = "none";
        }
    });

});
</script>
<!-- Toast Container -->
<div id="toast-container" style="
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 2000;
"></div>

</body>
</html>
