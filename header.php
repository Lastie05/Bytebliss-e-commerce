<?php 
session_start ();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0 maximum-scale=1.0, user-scalable=yes">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <title>ByteBliss | Tech Paradise</title>
</head>
<body>
<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <a href="index.php">
                <img src="images/logo2.PNG" alt="ByteBliss" class="site-logo">
            </a>
        </div>
        
        <nav>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#offers">Offers</a></li>
                <li><a href="#products">Products</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>

        <div class="nav-right">
            <div class="search-wrapper">
                <input type="text" id="product-search" placeholder="Search gadgets..." class="modern-search">
                <button class="search-btn" onclick="handleSearch()"><i class="fas fa-search"></i></button>
            </div>

           
            <div class="cart-wrapper" onclick="toggleCart()" style="cursor: pointer; position: relative;">
                <i class="fas fa-shopping-bag"></i>
                <span id="cart-badge" class="cart-badge" style="display: none;">0</span>
            </div>
           

           <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-profile">
            <i class="fas fa-user-circle"></i>
            <span class="cyan-text">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    <?php else: ?>
        <button class="btn-login-compact" onclick="openAuthModal('login')">Login</button>
    <?php endif; ?>

        </div>

    </div>
</header>