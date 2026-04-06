<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']) ? 'true' : 'false';
?>

<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Hot Offers | ByteBliss</title>
</head>
<body>

<main class="offers-view">
    <div class="offers-intro">
        <span class="mini-title">FEATURED DEALS</span>
        <h1>Explore Our <span class="text-gradient">Current Offers</span></h1>
        <p>Don't miss out! Grab the finest tech at amazing prices.</p>
    </div>

    <div class="offer-slider-container">
        <button class="slider-arrow prev" onclick="plusSlides(-1)">&#10094;</button>
        
        <div class="slider-window">
            <?php
            $sql = "SELECT * FROM products WHERE is_offer = 1";
            $result = mysqli_query($conn, $sql);
            
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                     $imgPath = 'images/' . $row['image_url'];
                     $productName = addslashes($row['name']);
                ?>
                    <div class="offer-slide fade">
                        <div class="glass-card">
                            <div class="flash-tag">FLASH DEAL!</div>
                                <div class="product-info">
                                     <div class="img-container">
                                       <img src="images/<?php echo $row['image_url']; ?>" alt="Offer">
                                     </div>
        
                                    <h2><?php echo $row['name']; ?></h2>
                                    <p class="was-price">Was: KSh <?php echo number_format($row['price'] * 1.25); ?></p>
                                    <p class="now-price">Now: KSh <?php echo number_format($row['price']); ?></p>
                                    <?php 
                                    // Prepare the image path and escape the name properly
                                    $imgPath = 'images/' . $row['image_url'];
                                    $productName = addslashes($row['name']);
                                    ?>
                                    <button class="get-btn" 
                                       onclick='addToCart(
                                       <?php echo $row['id']; ?>, 
                                       "<?php echo $productName; ?>", 
                                       <?php echo $row['price']; ?>, 
                                       "<?php echo $imgPath; ?>"
                                       )'>
                                       Get Yours →
                                    </button>
                                </div>
                            </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>

        <button class="slider-arrow next" onclick="plusSlides(1)">&#10095;</button>
    </div>

    <div class="dot-container">
        <span class="dot" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
        <span class="dot" onclick="currentSlide(3)"></span>
    </div>
</main>

<script>
    let slideIndex = 1;
    let autoSlideTimeout;

    // Wait for page to load before checking offers
    document.addEventListener('DOMContentLoaded', function() {
        let slides = document.getElementsByClassName("offer-slide");
        
        // Only run slider if there are offers
        if (slides.length > 0) {
            showSlides(slideIndex);
            startAutoSlide();
        } else {
            console.log("No offers to display");
            // Optional: Hide slider controls
            let sliderContainer = document.querySelector('.offer-slider-container');
            if (sliderContainer) {
                sliderContainer.style.display = 'none';
            }
            let dotContainer = document.querySelector('.dot-container');
            if (dotContainer) {
                dotContainer.style.display = 'none';
            }
        }
    });

    function plusSlides(n) {
        let slides = document.getElementsByClassName("offer-slide");
        if (slides.length === 0) return;
        showSlides(slideIndex += n);
        resetTimer();
    }

    function currentSlide(n) {
        let slides = document.getElementsByClassName("offer-slide");
        if (slides.length === 0) return;
        showSlides(slideIndex = n);
        resetTimer();
    }

    function showSlides(n) {
        let slides = document.getElementsByClassName("offer-slide");
        let dots = document.getElementsByClassName("dot");
        
        // CRITICAL: Exit if no slides
        if (slides.length === 0) {
            return;
        }
        
        if (n > slides.length) {slideIndex = 1}    
        if (n < 1) {slideIndex = slides.length}
        
        // Hide all slides
        for (let i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";  
        }
        
        // Deactivate all dots - ONLY if dots exist
        if (dots.length > 0) {
            for (let i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
        }
        
        // Show the current slide
        slides[slideIndex-1].style.display = "block";  
        
        // Highlight current dot - ONLY if dot exists
        if (dots.length > 0 && dots[slideIndex-1]) {
            dots[slideIndex-1].className += " active";
        }
    }

    function startAutoSlide() {
        let slides = document.getElementsByClassName("offer-slide");
        if (slides.length === 0) return;
        
        autoSlideTimeout = setInterval(function() {
            plusSlides(1);
        }, 5000);
    }

    function resetTimer() {
        clearInterval(autoSlideTimeout);
        startAutoSlide();
    }
</script>


</body>
</html>