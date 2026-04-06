

<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <title>ByteBliss | Tech Paradise</title>
</head>
<body>
    

     
    <?php include 'header.php'; ?>

    <section id="home">
        <?php include 'home.php'; ?>
    </section>

    <section id="offers">
        <?php include 'offers.php'; ?>
    </section>

    <section id="products">
        <?php include 'products.php'; ?>
    </section>

    <section id="about">
         <?php include 'about.php'; ?>
    </section>

    <section id="contact">
        <?php include 'contact.php'; ?>
    </section>

    <?php include 'footer.php'; ?>

        <button id="scrollTopBtn" title="Go to top">
         <i class="fas fa-chevron-up"></i>
        </button>

<script>
    const scrollTopBtn = document.getElementById("scrollTopBtn");

    // Show the button when the user scrolls down 400px
    window.onscroll = function() {
        if (document.body.scrollTop > 400 || document.documentElement.scrollTop > 400) {
            scrollTopBtn.style.display = "flex";
            scrollTopBtn.style.opacity = "1";
        } else {
            scrollTopBtn.style.opacity = "0";
            // Delay display none to allow fade out
            setTimeout(() => { if(scrollTopBtn.style.opacity === "0") scrollTopBtn.style.display = "none"; }, 300);
        }
    };

    // Smooth scroll to top function
    scrollTopBtn.onclick = function() {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    };
</script>



<div id="auth-modal" class="login-overlay">
    <div class="auth-container">
        <button class="auth-close" onclick="closeAuthModal()">&times;</button>
        
        <div class="auth-tabs">
            <button id="tab-login" class="active" onclick="switchTab('login')">Login</button>
            <button id="tab-signup" onclick="switchTab('signup')">Sign Up</button>
            <div class="tab-indicator"></div>
        </div>

        <form id="form-login" class="auth-form active" action="login_process.php" method="POST">
            <h2>Welcome back</h2>
            <p>Sign in to your account</p>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="you@example.com" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <a href="javascript:void(0)" class="forgot-pass" onclick="switchTab('reset')">Forgot password?</a>
            <button type="submit" class="auth-btn">Sign In</button>
            <p class="auth-switch-text">Don't have an account? <span onclick="switchTab('signup')">Sign up</span></p>
        </form>

        <form id="form-signup" class="auth-form" action="signup_process.php" method="POST">
            <h2>Create account</h2>
            <p>Join us today — it's free</p>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="full_name" placeholder="John Doe" required>
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="you@example.com" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Min. 6 characters" required>
            </div>
            <button type="submit" class="auth-btn">Create Account</button>
            <p class="auth-switch-text">Already have an account? <span onclick="switchTab('login')">Sign in</span></p>
        </form>
        
        <form id="form-reset" class="auth-form" action="reset_request.php" method="POST" style="display:none;">
            <h2>Reset Password</h2>
            <p>Enter your email to receive a reset link.</p>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="you@example.com" required>
            </div>
            <button type="submit" class="auth-btn">Send Link</button>
            <p class="auth-switch-text">Remembered? <span onclick="switchTab('login')">Sign in</span></p>
        </form>
    </div>
</div>




<script>
    // Bridge PHP session to a Global JS variable
    window.ByteBliss_IsLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
</script>

<?php include 'cart_view.php'; ?>

<?php include 'payments_modal.php'; ?>

<script src="interactions.js"></script>

</body>
</html>
