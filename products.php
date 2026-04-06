<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'db.php'; ?>

   
<main class="products-container">
    <div class="products-header">
        <span class="mini-label">OUR COLLECTION</span>
        <h1>Featured <span class="text-gradient">Electronics</span></h1>
        <p>Explore our wide range of cutting-edge electronics and find the perfect device for your needs.</p>
    </div>

    <div class="filter-bar">
        <button class="filter-btn active" onclick="filterSelection('all')">All</button>
        <button class="filter-btn" onclick="filterSelection('Smartphones')">Smartphones</button>
        <button class="filter-btn" onclick="filterSelection('Laptops')">Laptops</button>
        <button class="filter-btn" onclick="filterSelection('Audio')">Audio</button>
        <button class="filter-btn" onclick="filterSelection('Wearables')">Wearables</button>
    </div>

    <div class="product-gallery">
        <?php
        $sql = "SELECT * FROM products";
        $result = mysqli_query($conn, $sql);

        while($row = mysqli_fetch_assoc($result)) {
            // We add the category as a class name for the filtering logic
            $id    = $row['id'];
            $name  = addslashes($row['name']); // Prevents errors 
            $price = $row['price'];
            $img   = "images/" . $row['image_url']; 
            // 2. Use json_encode for the name and image path to escape quotes automatically
            $jsonName = json_encode($name);
            $jsonImg  = json_encode($img);
            ?>


            <div class="product-item <?php echo $row['category']; ?>" data-name="<?php echo strtolower($row['name']); ?>">
                <div class="product-card-v2">
                    <span class="featured-badge">Featured</span>
                    <div class="product-img-box">
                        <img src="images/<?php echo $row['image_url']; ?>" alt="Device">
                    </div>
                    <div class="product-info-v2">
                        <span class="category-name"><?php echo $row['category']; ?></span>
                        <h3><?php echo $row['name']; ?></h3>
                        <p class="price-tag">KSh <?php echo number_format($row['price']); ?></p>
                    </div>
                   <div class="product-actions">
                        <?php 
                        // Prepare image path properly
                        $imgPath = 'images/' . $row['image_url'];
                        ?>
                        <button class="btn-add-cart" 
                           onclick='addToCart(
                           <?php echo $row['id']; ?>, 
                           <?php echo json_encode($row['name']); ?>, 
                           <?php echo $row['price']; ?>, 
                           <?php echo json_encode($imgPath); ?>
                           )'>
                          Add to Cart
                        </button>
                        <button class="btn-buy-now" 
                           onclick='addToCart(
                           <?php echo $row['id']; ?>, 
                           <?php echo json_encode($row['name']); ?>, 
                           <?php echo $row['price']; ?>, 
                           <?php echo json_encode($imgPath); ?>
                            ); setTimeout(function(){ toggleCart(); }, 300);'>
                           
                          Buy Now →
                        </button>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</main>

<script>
// Simple filtering logic
function filterSelection(c) {
  var x, i;
  x = document.getElementsByClassName("product-item");
  if (c == "all") c = "";
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";
    if (x[i].className.indexOf(c) > -1) {
        x[i].style.display = "block";
    }
  }
  // Add active class to button
  var btns = document.getElementsByClassName("filter-btn");
  for (var i = 0; i < btns.length; i++) {
      btns[i].classList.remove("active");
  }
}
</script>

<script src="interactions.js"></script>
</body>
</html>
