// Sample user data
let currentUser = {
    name: "Jane Doe",
    email: "jane.doe@example.com",
    phone: "+1 (555) 123-4567",
    address: {
        street: "123 UserHub Lane",
        city: "Metropolis",
        state: "CA",
        zipCode: "90210"
    },
    notifications: true
};

// Sample products data
let products = [
    {
        id: 1,
        name: "Smartwatch Pro",
        description: "Advanced smartwatch with health monitoring and GPS tracking.",
        price: 199.99,
        category: "electronics",
        addedToBill: false
    },
    {
        id: 2,
        name: "Ergonomic Office Chair",
        description: "High-back ergonomic chair with lumbar support for maximum comfort.",
        price: 349.00,
        category: "home",
        addedToBill: true
    },
    {
        id: 3,
        name: "Wireless Bluetooth Earbuds",
        description: "Premium sound quality with noise cancellation and 24-hour battery.",
        price: 89.50,
        category: "electronics",
        addedToBill: false
    },
    {
        id: 4,
        name: "Professional Espresso Machine",
        description: "Brew cafe-quality espresso at home with this powerful machine.",
        price: 599.99,
        category: "home",
        addedToBill: false
    },
    {
        id: 5,
        name: "The Alchemist",
        description: "A philosophical novel by Paulo Coelho about following your dreams.",
        price: 15.99,
        category: "books",
        addedToBill: false
    },
    {
        id: 6,
        name: "Noise-Cancelling Headphones",
        description: "Immersive audio experience with advanced noise-cancelling technology.",
        price: 249.00,
        category: "electronics",
        addedToBill: true
    }
];

// Bill items (items added to bill)
let billItems = [
    {
        id: 2,
        name: "Ergonomic Office Chair",
        price: 349.00,
        quantity: 1
    },
    {
        id: 6,
        name: "Noise-Cancelling Headphones",
        price: 249.00,
        quantity: 1
    }
];

// Load user information
function loadUserInfo() {
    const userName = document.getElementById('userName');
    const userEmail = document.getElementById('userEmail');
    
    if (userName && userEmail) {
        userName.textContent = currentUser.name;
        userEmail.textContent = currentUser.email;
    }
}

// Load products on dashboard
function loadProducts() {
    const productsGrid = document.getElementById('productsGrid');
    if (!productsGrid) return;
    
    productsGrid.innerHTML = '';
    
    // Show only 6 products on dashboard
    const displayProducts = products.slice(0, 6);
    
    displayProducts.forEach(product => {
        const productCard = createProductCard(product);
        productsGrid.appendChild(productCard);
    });
}

// Create product card HTML
function createProductCard(product) {
    const card = document.createElement('div');
    card.className = 'product-card';
    card.innerHTML = `
        <div class="product-image">
            <i class="fas fa-${getProductIcon(product.category)}"></i>
        </div>
        <div class="product-content">
            <div class="product-category">${product.category.toUpperCase()}</div>
            <h3 class="product-title">${product.name}</h3>
            <p class="product-description">${product.description}</p>
            <div class="product-footer">
                <div class="product-price">$${product.price.toFixed(2)}</div>
                <button class="add-to-bill" onclick="addToBill(${product.id})" 
                        ${product.addedToBill ? 'disabled style="opacity:0.6"' : ''}>
                    <i class="fas fa-cart-plus"></i>
                    ${product.addedToBill ? 'Added to Bill' : 'Add to Bill'}
                </button>
            </div>
        </div>
    `;
    return card;
}

// Get icon based on product category
function getProductIcon(category) {
    const icons = {
        'electronics': 'mobile-alt',
        'home': 'home',
        'apparel': 'tshirt',
        'books': 'book'
    };
    return icons[category] || 'box';
}

// Add product to bill
function addToBill(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;
    
    // Check if already in bill
    const existingItem = billItems.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        billItems.push({
            id: product.id,
            name: product.name,
            price: product.price,
            quantity: 1
        });
        product.addedToBill = true;
    }
    
    // Update bill count
    updateBillCount();
    
    // Show success message
    showNotification(`${product.name} added to bill!`);
    
    // Refresh products display
    if (document.getElementById('productsGrid')) {
        loadProducts();
    }
    
    // Save to localStorage
    saveToLocalStorage();
}

// Update bill count in sidebar
function updateBillCount() {
    const badge = document.getElementById('pendingBills');
    if (badge) {
        badge.textContent = billItems.length;
    }
}

// Filter products by category
function filterProducts(category) {
    const tabs = document.querySelectorAll('.tab-btn');
    tabs.forEach(tab => {
        if (tab.textContent.toLowerCase().includes(category)) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
    
    const productsGrid = document.getElementById('productsGrid');
    if (!productsGrid) return;
    
    productsGrid.innerHTML = '';
    
    let filteredProducts = products;
    if (category !== 'all') {
        filteredProducts = products.filter(p => p.category === category);
    }
    
    filteredProducts.forEach(product => {
        const productCard = createProductCard(product);
        productsGrid.appendChild(productCard);
    });
}

// Load bill items on bill page
function loadBillItems() {
    const billTable = document.getElementById('billTable');
    const emptyBill = document.getElementById('emptyBill');
    const billItemsContainer = document.getElementById('billItems');
    const subtotalElement = document.getElementById('subtotal');
    const taxElement = document.getElementById('tax');
    const totalElement = document.getElementById('total');
    
    if (!billTable || !emptyBill) return;
    
    if (billItems.length === 0) {
        billTable.style.display = 'none';
        emptyBill.style.display = 'block';
        return;
    }
    
    billTable.style.display = 'table';
    emptyBill.style.display = 'none';
    
    // Clear existing items
    if (billItemsContainer) {
        billItemsContainer.innerHTML = '';
    }
    
    let subtotal = 0;
    
    // Add each bill item to table
    billItems.forEach((item, index) => {
        const row = document.createElement('tr');
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        
        row.innerHTML = `
            <td>${item.name}</td>
            <td>$${item.price.toFixed(2)}</td>
            <td>
                <div class="quantity-control">
                    <button class="qty-btn" onclick="updateQuantity(${index}, -1)">-</button>
                    <span>${item.quantity}</span>
                    <button class="qty-btn" onclick="updateQuantity(${index}, 1)">+</button>
                </div>
            </td>
            <td>$${itemTotal.toFixed(2)}</td>
            <td>
                <button class="remove-btn" onclick="removeFromBill(${index})">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </td>
        `;
        
        if (billItemsContainer) {
            billItemsContainer.appendChild(row);
        }
    });
    
    // Calculate totals
    const tax = subtotal * 0.08; // 8% tax
    const total = subtotal + tax;
    
    // Update total displays
    if (subtotalElement) subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
    if (taxElement) taxElement.textContent = `$${tax.toFixed(2)}`;
    if (totalElement) totalElement.textContent = `$${total.toFixed(2)}`;
}

// Update item quantity in bill
function updateQuantity(itemIndex, change) {
    const item = billItems[itemIndex];
    item.quantity += change;
    
    if (item.quantity < 1) {
        removeFromBill(itemIndex);
    } else {
        // Save and reload
        saveToLocalStorage();
        loadBillItems();
        updateBillCount();
    }
}

// Remove item from bill
function removeFromBill(itemIndex) {
    const item = billItems[itemIndex];
    
    // Mark product as not in bill
    const product = products.find(p => p.id === item.id);
    if (product) {
        product.addedToBill = false;
    }
    
    // Remove from bill items
    billItems.splice(itemIndex, 1);
    
    // Save and reload
    saveToLocalStorage();
    loadBillItems();
    updateBillCount();
    
    // Refresh products if on products page
    if (document.getElementById('productsGrid')) {
        loadProducts();
    }
    
    showNotification(`${item.name} removed from bill`);
}

// Checkout function
function checkout() {
    if (billItems.length === 0) {
        showNotification('Your bill is empty!', 'error');
        return;
    }
    
    // Calculate total
    const subtotal = billItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const tax = subtotal * 0.08;
    const total = subtotal + tax;
    
    // Show confirmation
    if (confirm(`Proceed to checkout? Total: $${total.toFixed(2)}`)) {
        // In real app, this would process payment
        showNotification('Payment processed successfully!', 'success');
        
        // Clear bill after successful checkout
        billItems.forEach(item => {
            const product = products.find(p => p.id === item.id);
            if (product) {
                product.addedToBill = false;
            }
        });
        
        billItems = [];
        saveToLocalStorage();
        loadBillItems();
        updateBillCount();
        
        // Refresh products if on products page
        if (document.getElementById('productsGrid')) {
            loadProducts();
        }
    }
}

// Load profile data
function loadProfileData() {
    const firstName = document.getElementById('firstName');
    const lastName = document.getElementById('lastName');
    const email = document.getElementById('email');
    const phone = document.getElementById('phone');
    const street = document.getElementById('street');
    const city = document.getElementById('city');
    const state = document.getElementById('state');
    const zipCode = document.getElementById('zipCode');
    const notifications = document.getElementById('notifications');
    
    if (firstName) firstName.value = currentUser.name.split(' ')[0];
    if (lastName) lastName.value = currentUser.name.split(' ')[1] || '';
    if (email) email.value = currentUser.email;
    if (phone) phone.value = currentUser.phone;
    if (street) street.value = currentUser.address.street;
    if (city) city.value = currentUser.address.city;
    if (state) state.value = currentUser.address.state;
    if (zipCode) zipCode.value = currentUser.address.zipCode;
    if (notifications) notifications.checked = currentUser.notifications;
}

// Save profile data
function saveProfile() {
    const firstName = document.getElementById('firstName').value;
    const lastName = document.getElementById('lastName').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const street = document.getElementById('street').value;
    const city = document.getElementById('city').value;
    const state = document.getElementById('state').value;
    const zipCode = document.getElementById('zipCode').value;
    const notifications = document.getElementById('notifications').checked;
    
    // Update current user
    currentUser.name = `${firstName} ${lastName}`;
    currentUser.email = email;
    currentUser.phone = phone;
    currentUser.address = {
        street: street,
        city: city,
        state: state,
        zipCode: zipCode
    };
    currentUser.notifications = notifications;
    
    // Update display
    loadUserInfo();
    
    // Save to localStorage
    saveToLocalStorage();
    
    showNotification('Profile updated successfully!', 'success');
    return false; // Prevent form submission
}

// Show notification
function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Add styles if not already present
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 5px;
                color: white;
                display: flex;
                align-items: center;
                gap: 10px;
                z-index: 1000;
                animation: slideIn 0.3s ease;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
            .notification.success {
                background: #1dd1a1;
            }
            .notification.error {
                background: #ff6b6b;
            }
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Search products
function searchProducts() {
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    
    const productsGrid = document.getElementById('productsGrid');
    if (!productsGrid) return;
    
    productsGrid.innerHTML = '';
    
    let filteredProducts = products;
    
    if (searchTerm) {
        filteredProducts = products.filter(product => 
            product.name.toLowerCase().includes(searchTerm) ||
            product.description.toLowerCase().includes(searchTerm) ||
            product.category.toLowerCase().includes(searchTerm)
        );
    }
    
    if (filteredProducts.length === 0) {
        productsGrid.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h3>No products found</h3>
                <p>Try a different search term</p>
            </div>
        `;
        return;
    }
    
    filteredProducts.forEach(product => {
        const productCard = createProductCard(product);
        productsGrid.appendChild(productCard);
    });
}

// Filter products by category on products page
function filterByCategory(category) {
    const filterOptions = document.querySelectorAll('.filter-option');
    filterOptions.forEach(option => {
        if (option.textContent === category) {
            option.classList.add('active');
        } else {
            option.classList.remove('active');
        }
    });
    
    const productsGrid = document.getElementById('productsGrid');
    if (!productsGrid) return;
    
    productsGrid.innerHTML = '';
    
    let filteredProducts = products;
    if (category !== 'All Categories') {
        filteredProducts = products.filter(p => 
            p.category.toLowerCase() === category.toLowerCase().replace(' ', '')
        );
    }
    
    filteredProducts.forEach(product => {
        const productCard = createProductCard(product);
        productsGrid.appendChild(productCard);
    });
}

// Sort products
function sortProducts(criteria) {
    let sortedProducts = [...products];
    
    switch(criteria) {
        case 'price-low':
            sortedProducts.sort((a, b) => a.price - b.price);
            break;
        case 'price-high':
            sortedProducts.sort((a, b) => b.price - a.price);
            break;
        case 'name':
            sortedProducts.sort((a, b) => a.name.localeCompare(b.name));
            break;
        default:
            // Default sorting
            break;
    }
    
    const productsGrid = document.getElementById('productsGrid');
    if (!productsGrid) return;
    
    productsGrid.innerHTML = '';
    sortedProducts.forEach(product => {
        const productCard = createProductCard(product);
        productsGrid.appendChild(productCard);
    });
}

// Save data to localStorage
function saveToLocalStorage() {
    localStorage.setItem('userHub_user', JSON.stringify(currentUser));
    localStorage.setItem('userHub_products', JSON.stringify(products));
    localStorage.setItem('userHub_billItems', JSON.stringify(billItems));
}

// Load data from localStorage
function loadFromLocalStorage() {
    const savedUser = localStorage.getItem('userHub_user');
    const savedProducts = localStorage.getItem('userHub_products');
    const savedBillItems = localStorage.getItem('userHub_billItems');
    
    if (savedUser) currentUser = JSON.parse(savedUser);
    if (savedProducts) products = JSON.parse(savedProducts);
    if (savedBillItems) billItems = JSON.parse(savedBillItems);
    
    updateBillCount();
}

// Logout function
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        // Clear session data
        localStorage.removeItem('userHub_user');
        localStorage.removeItem('userHub_billItems');
        
        // Redirect to login page
        window.location.href = 'login.html';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadFromLocalStorage();
    loadUserInfo();
    
    // Check which page we're on and load appropriate content
    if (document.getElementById('productsGrid')) {
        loadProducts();
    }
    
    if (document.getElementById('billItems')) {
        loadBillItems();
    }
    
    if (document.getElementById('firstName')) {
        loadProfileData();
    }
    
    // Initialize bill count
    updateBillCount();
});