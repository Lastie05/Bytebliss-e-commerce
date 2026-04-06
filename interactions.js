// A. State Management

let cart = [];

// Load cart from localStorage on page load
function loadCart() {
    const savedCart = localStorage.getItem('bytebliss_cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
    } else {
        cart = [];
    }
    updateCartUI();
}

// Save cart to localStorage
function saveCart() {
    localStorage.setItem('bytebliss_cart', JSON.stringify(cart));
    updateCartUI();
}

// B. Toggle Sidebar Function
function toggleCart() {
    const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-overlay');
    
    // These classes match the new CSS (.active) for sliding and blurring
    if (sidebar && overlay) {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }
}


// C. Add to Cart Functionality
function addToCart(id, name, price, img) {
    console.log("Adding to cart:", {id, name, price, img});
    
    // Load cart from localStorage
    let currentCart = JSON.parse(localStorage.getItem('bytebliss_cart')) || [];
    
    // Check if item already exists
    const existingItem = currentCart.find(item => item.id == id);
    
    if (existingItem) {
        // Increment quantity
        existingItem.qty = (existingItem.qty || 1) + 1;
        console.log("Item exists, new quantity:", existingItem.qty);
    } else {
        // Add new item
        currentCart.push({ 
            id: id, 
            name: name, 
            price: parseFloat(price), 
            img: img, 
            qty: 1 
        });
        console.log("New item added:", name);
    }
    
    // Save to localStorage
    localStorage.setItem('bytebliss_cart', JSON.stringify(currentCart));
    
    // Update UI
    updateCartUI();
    
    // Show notification
    showNotification(`${name} added to cart!`);
    
    // Auto-open cart after adding
    const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-overlay');
    
   if (sidebar && !sidebar.classList.contains('active')) {
        sidebar.classList.add('active');
        if (overlay) overlay.classList.add('active');
    }
}


// D. UI Update Engine
function updateCartUI() {
    const currentCart = JSON.parse(localStorage.getItem('bytebliss_cart')) || [];
    const sidebarList = document.getElementById('cart-items');
    const badge = document.getElementById('cart-badge');
    const totalDisplay = document.getElementById('cart-total');
    let totalValue = 0;

    console.log("Updating cart UI. Items in cart:", currentCart.length);
    console.log("Cart data:", currentCart); // Debug: see what's in the cart

    // 1. Update Navbar Badge
     if (currentCart.length > 0) {
        const totalQty = currentCart.reduce((acc, item) => acc + (item.qty || 1), 0);
        if (badge) {
            badge.innerText = totalQty;
            badge.style.display = 'flex';
        }
    } else {
        if (badge) badge.style.display = 'none';
    }

    // 2. Render Sidebar Content
     if (!sidebarList) return;
    
    if (currentCart.length === 0) {
        // High-end Empty State (matches the CSS we just created)
        sidebarList.innerHTML = `
            <div class="empty-cart-view">
                <div class="empty-icon">
                    <i class="fas fa-shopping-bag">🛍️</i>
                </div>
                <h4>Your bag is empty</h4>
                <p>Looks like you haven't added any digital bliss to your bag yet.</p>
                <button class="btn-shop-now" onclick="toggleCart()">Start Shopping</button>
            </div>
        `;
        if (totalDisplay) totalDisplay.innerText = 'KSh 0';
    } else {
    
         // Render Product List
         sidebarList.innerHTML = currentCart.map((item, index) => {
            const itemTotal = (item.price || 0) * (item.qty || 1);
            totalValue += itemTotal;
            
            // Handle image path - ensure it has the correct format
            let imgSrc = item.img || 'images/placeholder.png';
            
            // If the image path doesn't start with 'images/', add it
            if (imgSrc && !imgSrc.startsWith('images/') && !imgSrc.startsWith('http')) {
                imgSrc = 'images/' + imgSrc;
            }
            
            return `
                <div class="cart-item" data-index="${index}">
                    <div class="cart-item-image">
                        <img src="${imgSrc}" alt="${escapeHtml(item.name)}" onerror="this.src='images/placeholder.png'">
                    </div>
                    <div class="cart-item-details">
                        <h4>${escapeHtml(item.name)}</h4>
                        <p class="cart-item-price">KSh ${(item.price || 0).toLocaleString()}</p>
                        <div class="cart-item-actions">
                            <button class="qty-btn" onclick="updateQuantity(${index}, ${(item.qty || 1) - 1})">-</button>
                            <span class="item-qty">${item.qty || 1}</span>
                            <button class="qty-btn" onclick="updateQuantity(${index}, ${(item.qty || 1) + 1})">+</button>
                            <button class="remove-item" onclick="removeFromCart(${index})">Remove</button>
                        </div>
                    </div>
                    <div class="cart-item-total">
                        <span>KSh ${itemTotal.toLocaleString()}</span>
                    </div>
                </div>
            `;
        }).join('');
        
        if (totalDisplay) totalDisplay.innerText = `KSh ${totalValue.toLocaleString()}`;
    }
}

// Helper function to escape HTML to prevent XSS
function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// Function to update quantity of cart items
function updateQuantity(index, newQty) {
    if (newQty <= 0) {
        removeFromCart(index);
    } else {
        let currentCart = JSON.parse(localStorage.getItem('bytebliss_cart')) || [];
        if (currentCart[index]) {
            currentCart[index].qty = newQty;
            localStorage.setItem('bytebliss_cart', JSON.stringify(currentCart));
            updateCartUI();
        }
    }
}

// E. Remove Item Function
function removeFromCart(index) {
    // Get fresh cart from localStorage
    let currentCart = JSON.parse(localStorage.getItem('bytebliss_cart')) || [];
    
    // Remove the item at the specified index
    currentCart.splice(index, 1);
    
    // Save back to localStorage
    localStorage.setItem('bytebliss_cart', JSON.stringify(currentCart));
    
    // Update the UI
    updateCartUI();
    
    // Show notification
    showNotification("Item removed from cart");
}


// Simple Search Functionality 
function handleSearch() {
    const searchTerm = document.getElementById('product-search').value.toLowerCase().trim();
    console.log("Searching for:", searchTerm);
    
    // Get all product items - use the correct class name
    const allProducts = document.querySelectorAll('.product-item');
    console.log("Total products found:", allProducts.length);
    
    let foundCount = 0;
    let firstFoundProduct = null;  // NEW: Track the first product found
    
    if (searchTerm === "") {
        // Show all products
        allProducts.forEach(product => {
            product.style.display = "block";
        });
        scrollToProducts();  // NEW: Scroll to products section
        return;
    }
    
    // Loop through each product
    allProducts.forEach(product => {
        // Get product name from the h3 tag inside the product
        const productNameElement = product.querySelector('h3');
        const productName = productNameElement ? productNameElement.innerText.toLowerCase() : "";
        
        // Also check category if needed
        const categoryElement = product.querySelector('.category-name');
        const category = categoryElement ? categoryElement.innerText.toLowerCase() : "";
        
        console.log("Checking product:", productName, "Category:", category);
        
        // Check if search term matches product name or category
        if (productName.includes(searchTerm) || category.includes(searchTerm)) {
            product.style.display = "block";
            foundCount++;
            // NEW: Store the first found product
            if (!firstFoundProduct) {
                firstFoundProduct = product;
            }
        } else {
            product.style.display = "none";
        }
    });
    
    // Show message if no products found
    if (foundCount === 0) {
        alert(`No products found for "${searchTerm}". Try searching for: iPhone, Samsung, Laptop, Headphones, etc.`);
        // Reset to show all products
        allProducts.forEach(product => {
            product.style.display = "block";
        });
        scrollToProducts();  // NEW: Still scroll to products section
    } else {
        console.log(`Found ${foundCount} product(s) matching "${searchTerm}"`);
        
        // NEW: Scroll to the products section
        scrollToProducts();
        
        // NEW: Highlight the first found product after scrolling
        setTimeout(() => {
            if (firstFoundProduct) {
                highlightProduct(firstFoundProduct);
            }
        }, 500);
    }
}

// NEW FUNCTION: Scroll to products section
function scrollToProducts() {
    const productsSection = document.getElementById('products');
    if (productsSection) {
        productsSection.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
        console.log("Scrolling to products section");
    } else {
        // Fallback: try to find products container
        const productsContainer = document.querySelector('.products-container');
        if (productsContainer) {
            productsContainer.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }
    }
}

// NEW FUNCTION: Highlight a product
function highlightProduct(product) {
    // Save original styles
    const originalTransition = product.style.transition;
    const originalBoxShadow = product.style.boxShadow;
    const originalBorder = product.style.border;
    
    // Apply highlight
    product.style.transition = 'all 0.3s ease';
    product.style.boxShadow = '0 0 20px rgba(0, 212, 255, 0.8)';
    product.style.border = '2px solid #00d4ff';
    
    // Remove highlight after 2 seconds
    setTimeout(() => {
        product.style.transition = originalTransition;
        product.style.boxShadow = originalBoxShadow;
        product.style.border = originalBorder;
    }, 2000);
}

function openAuthModal() {
    document.getElementById('auth-modal').style.display = 'flex';
}

function closeAuthModal() {
    document.getElementById('auth-modal').style.display = 'none';
}

function switchTab(type) {
    const loginForm = document.getElementById('form-login');
    const signupForm = document.getElementById('form-signup');
    const resetForm = document.getElementById('form-reset');
    
    const loginTab = document.getElementById('tab-login');
    const signupTab = document.getElementById('tab-signup');
    const indicator = document.querySelector('.tab-indicator');

    // Hide all forms
    loginForm.style.display = 'none';
    signupForm.style.display = 'none';
    if(resetForm) resetForm.style.display = 'none';
    
    // Reset tab colors
    loginTab.classList.remove('active');
    signupTab.classList.remove('active');

    if (type === 'login') {
        loginForm.style.display = 'block';
        loginTab.classList.add('active');
        indicator.style.transform = 'translateX(0%)'; // Moves to Login
    } 
    else if (type === 'signup') {
        signupForm.style.display = 'block';
        signupTab.classList.add('active');
        indicator.style.transform = 'translateX(100%)'; // Slides to Sign Up
    }
    else if (type === 'reset') {
        if(resetForm) resetForm.style.display = 'block';
        // Keep the underline on the login tab while resetting
        indicator.style.transform = 'translateX(0%)'; 
    }
}

/* --- SECTION A: Modal Control --- */

function openPaymentModal() {
    const modal = document.getElementById('payments-modal');
    
    if (modal) {

        // Close cart sidebar if open
        const cartSidebar = document.getElementById('cart-sidebar');
        if (cartSidebar && cartSidebar.classList.contains('active')) {
            cartSidebar.classList.remove('active');
            const overlay = document.getElementById('cart-overlay');
            if (overlay) overlay.classList.remove('active');
        }

        // 1. Force the modal and its background overlay to show
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // 2. Update the summary
        populateOrderSummary();
    }    
}

function closePaymentModal() {
    const modal = document.getElementById('payments-modal');
    if (modal) {
        // 1. Hide the entire modal container
        modal.style.display = 'none';
       
        // 2. Restore background scrolling
        document.body.style.overflow = 'auto';
    }
}

/* --- SECTION B: Checkout Entry Point --- */

function handleCheckoutClick(isLoggedIn) {
    const cart = JSON.parse(localStorage.getItem('bytebliss_cart')) || [];
    
    if (cart.length === 0) {
        alert("Your bag is empty!");
        return;
    }

    // Always close the cart sidebar first to prevent overlay stacking
   const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-overlay');
    
    if (sidebar && sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
    }

    // Check login status (provided by your PHP session)
    if (!isLoggedIn) {
        console.log("User not logged in, opening auth modal");
        setTimeout(() => {
            openAuthModal();
        }, 300);
    } else {
        setTimeout(() => {
            console.log("User logged in, opening payment modal");
            openPaymentModal();
            populateOrderSummary();
        }, 300);
    }
}

/* --- SECTION C: Data Population --- */

function populateOrderSummary() {
    const container = document.getElementById('summary-items');
    const totalDisplay = document.getElementById('final-total');
    let total = 0;

    // Fix: Redefine cart inside to ensure it's always fresh
    const cart = JSON.parse(localStorage.getItem('bytebliss_cart')) || [];

    if (cart.length === 0) {
        container.innerHTML = '<p style="text-align:center; padding:20px;">Your bag is empty.</p>';
        return;
    }

    container.innerHTML = cart.map(item => {
        const itemTotal = item.price * item.qty;
        total += itemTotal;
        return `
            <div class="summary-row" style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <span>${item.name} (x${item.qty})</span>
                <span class="cyan-text">KSh ${itemTotal.toLocaleString()}</span>
            </div>
        `;
    }).join('');

    totalDisplay.innerText = `KSh ${total.toLocaleString()}`;
}

/* --- SECTION D: The M-Pesa Process --- */

// Helper to get total from localStorage
function calculateTotal() {
    const cart = JSON.parse(localStorage.getItem('bytebliss_cart')) || [];
    return cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
}

function processOrder() {
    // Get all form elements
    const nameInput = document.getElementById('pay-name');
    const phoneInput = document.getElementById('pay-phone');
    const countySelect = document.getElementById('pay-county');
    const addressInput = document.getElementById('address');
    const mpesaPhoneInput = document.getElementById('mpesa-phone');
    const deliveryInstructionsInput = document.getElementById('delivery-instructions');
    const orderBtn = document.querySelector('.btn-checkout-final');
    
    // Validate cart
    const cart = validateAndPrepareCart();
    
    if (cart.length === 0) {
        alert("Your cart is empty!");
        return;
    }
    
    // Determine payment method
    const mpesaCard = document.getElementById('method-mpesa');
    const activeMethod = mpesaCard && mpesaCard.classList.contains('active') ? "M-Pesa" : "Cash";
    
    // Get phone number - prioritize M-Pesa specific field if M-Pesa is selected
    let customerPhone = phoneInput ? phoneInput.value.trim() : '';
    
    if (activeMethod === "M-Pesa" && mpesaPhoneInput && mpesaPhoneInput.value.trim()) {
        customerPhone = mpesaPhoneInput.value.trim();
    }
    
    // Get other values
    const fullName = nameInput ? nameInput.value.trim() : '';
    const county = countySelect ? countySelect.value : '';
    const address = addressInput ? addressInput.value.trim() : '';
    const deliveryInstructions = deliveryInstructionsInput ? deliveryInstructionsInput.value.trim() : '';
    
    // Debug log
    console.log("=== ORDER SUBMISSION ===");
    console.log("name:", fullName);
    console.log("phone:", customerPhone);
    console.log("county:", county);
    console.log("address:", address);
    console.log("payment_method:", activeMethod);
    console.log("cart items:", cart.length);
    console.log("=========================");
    
    // Validation
    if (!fullName) {
        alert("Please enter your full name.");
        nameInput?.focus();
        return;
    }
    
    if (!customerPhone) {
        alert("Please enter your phone number.");
        phoneInput?.focus();
        return;
    }
    
    if (!county) {
        alert("Please select your county.");
        countySelect?.focus();
        return;
    }
    
    if (!address) {
        alert("Please enter your delivery address.");
        addressInput?.focus();
        return;
    }
    
    // Format M-Pesa phone if needed
    let mpesaPhone = "";
    if (activeMethod === "M-Pesa") {
        mpesaPhone = customerPhone;
        // Remove non-numeric characters
        mpesaPhone = mpesaPhone.replace(/\D/g, '');
        if (mpesaPhone.startsWith('0')) mpesaPhone = '254' + mpesaPhone.substring(1);
        if (!mpesaPhone.startsWith('254')) mpesaPhone = '254' + mpesaPhone;
    }
    
    // Format customer phone for records
    let formattedCustomerPhone = customerPhone.replace(/\D/g, '');
    if (formattedCustomerPhone.startsWith('0')) {
        formattedCustomerPhone = '254' + formattedCustomerPhone.substring(1);
    }
    
    // Disable button
    if (orderBtn) {
        orderBtn.disabled = true;
        const btnText = orderBtn.querySelector('#btn-text') || orderBtn;
        btnText.innerHTML = activeMethod === "M-Pesa" ? 'Requesting PIN...' : 'Placing Order...';
        const spinner = document.getElementById('payment-spinner');
        if (spinner) spinner.style.display = 'inline-block';
    }
    
    // Prepare order data - EXACTLY matching backend expectations
    const orderData = {
        name: fullName,
        phone: formattedCustomerPhone,
        county: county,
        address: address,
        delivery_instructions: deliveryInstructions,
        mpesa_phone: mpesaPhone,
        amount: calculateTotal(),
        payment_method: activeMethod,
        cart: JSON.stringify(cart)
    };
    
    console.log("Sending to server:", orderData);
    
    // Send to server
    fetch('process_payment.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        console.log("Server response:", data);
        
        if (data.status === 'success') {
            localStorage.removeItem('bytebliss_cart');
            updateCartUI();
            
            if (activeMethod === "M-Pesa") {
                window.location.href = `processing.php?order_id=${data.order_id}`;
            } else {
                window.location.href = `success.php?order_id=${data.order_id}`;
            }
        } else {
            alert("Error: " + data.message);
            if (orderBtn) {
                orderBtn.disabled = false;
                const btnText = orderBtn.querySelector('#btn-text') || orderBtn;
                btnText.innerHTML = 'Confirm & Place Order →';
                const spinner = document.getElementById('payment-spinner');
                if (spinner) spinner.style.display = 'none';
            }
        }
    })
    .catch(err => {
        console.error('Order error:', err);
        alert("Could not connect to the server. Please try again.\n\nError: " + err.message);
        if (orderBtn) {
            orderBtn.disabled = false;
            const btnText = orderBtn.querySelector('#btn-text') || orderBtn;
            btnText.innerHTML = 'Confirm & Place Order →';
            const spinner = document.getElementById('payment-spinner');
            if (spinner) spinner.style.display = 'none';
        }
    });
}


/*Helper UI Functions */

function selectMethod(method) {
    const mpesaCard = document.getElementById('method-mpesa');
    const codCard = document.getElementById('method-cod');
    const mpesaFields = document.getElementById('mpesa-fields');

    if (method === 'mpesa') {
        mpesaCard.classList.add('active');
        codCard.classList.remove('active');
        mpesaFields.style.display = 'block';
    } else {
        codCard.classList.add('active');
        mpesaCard.classList.remove('active');
        mpesaFields.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartUI();
    
    // Initialize payment method selection
    const mpesaCard = document.getElementById('method-mpesa');
    const codCard = document.getElementById('method-cod');
    if(mpesaCard && codCard) {
        selectMethod('mpesa');
    }
});

// Validates cart before sending to server
function validateAndPrepareCart() {
    const cart = JSON.parse(localStorage.getItem('bytebliss_cart')) || [];
    
    // Validate each cart item has required fields
    const validCart = cart.filter(item => {
        return item.id && item.name && item.price && item.qty;
    });
    
    if (validCart.length !== cart.length) {
        console.warn('Some cart items were invalid and removed');
        localStorage.setItem('bytebliss_cart', JSON.stringify(validCart));
        updateCartUI();
    }
    
    return validCart;
}

// Show notification function
function showNotification(message) {
    // Create notification element if it doesn't exist
    let notification = document.getElementById('cart-notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'cart-notification';
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #2ecc71;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            z-index: 10001;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease;
        `;
        document.body.appendChild(notification);
    }
    
    notification.textContent = message;
    notification.style.display = 'block';
    
    setTimeout(() => {
        notification.style.display = 'none';
    }, 2000);
}

