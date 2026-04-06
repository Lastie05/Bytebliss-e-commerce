<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional: Check if the user is logged in to personalize the page
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? htmlspecialchars($_SESSION['user_name']) : "Guest";
?>
<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
   <section class="hero-section" id="home-section">
    <div class="hero-overlay">
        <span class="badge-top">Your Trusted Electronics Partner</span>
        <h1>Powering Your <span class="text-gradient">Digital Life</span></h1>
        <p class="hero-subtitle">Discover the latest electronics, cutting-edge technology, and exceptional service at ByteBliss. From smartphones to home appliances, we have everything you need.</p>
        
        <div class="hero-buttons">
            <a href="#products" class="btn-primary">Explore Products</a>
            <a href="#contact" class="btn-secondary">Contact Us</a>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <h2>500+</h2>
                <p>Products Available</p>
            </div>
            <div class="stat-card">
                <h2>10K+</h2>
                <p>Happy Customers</p>
            </div>
            <div class="stat-card">
                <h2>3+</h2>
                <p>Years Experience</p>
            </div>
        </div>
    </div>
</section>

</div>

<script>
    // home.php

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    // If we redirected from a checkout attempt, reopen the modal
    if (urlParams.has('checkout')) {
        openPaymentModal();
    }
});

</script>


</body>
</html>