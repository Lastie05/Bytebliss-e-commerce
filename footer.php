<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <footer class="main-footer">
    <div class="footer-container">
        <div class="footer-col brand-info">
            <div class="footer-logo">
                <span class="logo-text">ByteBliss <span class="bolt">⚡</span></span>
            </div>
            <p class="footer-bio">
                Your premier destination for high-end gadgets in Nairobi. 
                Bringing you the latest in tech innovation with a touch of bliss since 2024.
            </p>
            <div class="footer-socials">
                <a href="https://tiktok.com/ByteBliss_ke" target="_blank"><i class="fab fa-tiktok"></i></a>
                <a href="https://twitter.com/ByteBliss_ke" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="https://instagram.com/ByteBliss_ke" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="https://wa.me/254700000000"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#offers">Offers</a></li>
                <li><a href="#products">Products</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>

                <li><a href="track_order.php">📦 Track Your Order</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Product Categories</h4>
            <ul>
                <li><a href="#">Smartphones</a></li>
                <li><a href="#">Laptops & PCs</a></li>
                <li><a href="#">Audio & Music</a></li>
                <li><a href="#">Smart Home</a></li>
                <li><a href="#">Accessories</a></li>
            </ul>
        </div>

        <div class="footer-col newsletter">
    <h4>Newsletter</h4>
    <p>Subscribe to get special offers and updates.</p>
    <form class="newsletter-form" id="newsletterForm">
        <input type="email" id="subscriberEmail" placeholder="Your email" required>
        <button type="submit" id="subsBtn">➤</button>
    </form>
    <div id="newsletterMsg" style="margin-top: 10px; font-size: 0.8rem;"></div>
</div>

    </div>

    <div class="footer-bottom">
        <p>&copy; 2026 ByteBliss Electronics. All rights reserved.</p>
        <div class="footer-legal">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
        </div>
    </div>
</footer>

<a href="https://wa.me/254700000000" class="floating-chat">
    <i class="fab fa-whatsapp"></i>
    <span>Talk with Us</span>
</a>

<script>
document.getElementById('newsletterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const emailInput = document.getElementById('subscriberEmail');
    const msgDiv = document.getElementById('newsletterMsg');
    const btn = document.getElementById('subsBtn');

    // Visual feedback
    btn.innerHTML = "⏳";
    
    const formData = new FormData();
    formData.append('email', emailInput.value);

    fetch('subscribe.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        msgDiv.innerHTML = data;
        msgDiv.style.color = data.includes('Welcome') ? '#2ecc71' : '#ff4757';
        btn.innerHTML = "➤";
        if(data.includes('Welcome')) emailInput.value = '';
    })
    .catch(error => {
        msgDiv.innerHTML = "Connection error.";
        btn.innerHTML = "➤";
    });
});
</script>

<!-- Admin Login Modal -->
<div id="admin-login-modal" class="admin-login-overlay">
    <div class="admin-login-container">
        <div class="admin-login-header">
            <h3>🔒 Admin Access</h3>
            <button class="admin-login-close" onclick="closeAdminLoginModal()">&times;</button>
        </div>
        <div class="admin-login-body">
            <p>Please enter the admin password to access the dashboard.</p>
            <div class="admin-input-group">
                <i class="fas fa-key">🔑</i>
                <input type="password" id="admin-password-input" placeholder="Enter admin password" autocomplete="off">
            </div>
            <div id="admin-login-error" class="admin-error-msg" style="display: none;">
                ❌ Incorrect password. Please try again.
            </div>
            <button class="admin-login-btn" onclick="verifyAdminPassword()">Access Dashboard →</button>
        </div>
    </div>
</div>

<style>
/* Admin Login Modal Styles */
.admin-login-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(10px);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 10001;
}

.admin-login-container {
    background: #0b0b14;
    width: 100%;
    max-width: 400px;
    border-radius: 20px;
    border: 1px solid #00d4ff;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
    animation: fadeInUp 0.3s ease;
}

.admin-login-header {
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-login-header h3 {
    color: #00d4ff;
    margin: 0;
    font-size: 1.2rem;
}

.admin-login-close {
    background: rgba(255, 255, 255, 0.05);
    border: none;
    color: #fff;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.3s;
}

.admin-login-close:hover {
    background: #ff4757;
    transform: rotate(90deg);
}

.admin-login-body {
    padding: 30px;
}

.admin-login-body p {
    color: #aaa;
    margin-bottom: 20px;
    font-size: 0.9rem;
    text-align: center;
}

.admin-input-group {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 12px 15px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s ease;
}

.admin-input-group:focus-within {
    border-color: #00d4ff;
    background: rgba(0, 212, 255, 0.05);
    box-shadow: 0 0 10px rgba(0, 212, 255, 0.2);
}

.admin-input-group i {
    color: #00d4ff;
    font-size: 1.2rem;
}

.admin-input-group input {
    background: none;
    border: none;
    color: #fff;
    width: 100%;
    outline: none;
    font-size: 1rem;
}

.admin-input-group input::placeholder {
    color: rgba(255, 255, 255, 0.4);
}

.admin-login-btn {
    width: 100%;
    background: #00d4ff;
    color: #000;
    padding: 14px;
    border-radius: 12px;
    border: none;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 10px;
}

.admin-login-btn:hover {
    background: #2ecc71;
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(46, 204, 113, 0.3);
}

.admin-error-msg {
    background: rgba(255, 71, 87, 0.2);
    border: 1px solid #ff4757;
    color: #ff4757;
    padding: 10px;
    border-radius: 8px;
    font-size: 0.8rem;
    text-align: center;
    margin-bottom: 15px;
}

/* Floating Admin Button */
.admin-float-btn {
    position: fixed;
    bottom: 200px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: #2ecc71;
    color: #000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 9999;
    transition: all 0.3s ease;
    border: none;
}

.admin-float-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 25px rgba(46, 204, 113, 0.3);
}

.admin-float-btn i {
    font-size: 1.5rem;
    color: #000;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .admin-float-btn {
        bottom: 80px;
        right: 20px;
        width: 45px;
        height: 45px;
    }
    .admin-login-container {
        width: 90%;
        margin: 20px;
    }
}
</style>

<script>
// Admin login functions
function openAdminLoginModal() {
    document.getElementById('admin-login-modal').style.display = 'flex';
    document.getElementById('admin-password-input').focus();
}

function closeAdminLoginModal() {
    document.getElementById('admin-login-modal').style.display = 'none';
    document.getElementById('admin-password-input').value = '';
    document.getElementById('admin-login-error').style.display = 'none';
}

function verifyAdminPassword() {
    const password = document.getElementById('admin-password-input').value;
    const adminPassword = 'YourSecureAdminPassword123!'; //  YOUR SECURE PASSWORD!
    
    if (password === adminPassword) {
        // Correct password - redirect to admin dashboard
        window.location.href = 'admin_dashboard.php?access=' + encodeURIComponent(password);
    } else {
        // Wrong password - show error
        document.getElementById('admin-login-error').style.display = 'block';
        document.getElementById('admin-password-input').value = '';
        document.getElementById('admin-password-input').focus();
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('admin-login-modal');
    if (event.target === modal) {
        closeAdminLoginModal();
    }
});

// Allow Enter key to submit
document.addEventListener('keypress', function(event) {
    if (event.key === 'Enter' && document.getElementById('admin-login-modal').style.display === 'flex') 
        {
        verifyAdminPassword();
    }
});
</script>

<!-- Floating Admin Button -->
<button class="admin-float-btn" onclick="openAdminLoginModal()" title="Admin Panel">
    <i>🔒</i>
</button>
?>

</body>
</html>