<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : "Guest";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="cart-sidebar" class="cart-sidebar">
    <div class="cart-header">
        <h3>Your Tech Bag</h3>
        <button class="close-btn" onclick="toggleCart()">&times;</button>
    </div>
    
    <div id="cart-items" class="cart-items-container">
        <p class="empty-msg">Your bag is empty. Start adding bliss!</p>
    </div>

    <div class="cart-footer">
        <div class="total-section">
            <span>Total</span>
            <span id="cart-total">KSh 0</span>
        </div>
        
        <button class="checkout-btn" onclick="handleCheckoutClick(<?php echo $isLoggedIn; ?>)">
            Checkout &rarr;
        </button>
        
    </div>
</div>

<div id="cart-overlay" class="cart-overlay" onclick="toggleCart()"></div>


</body>
</html>