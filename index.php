<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get featured products (latest 6 products)
$products = getAllProducts();
$featured_products = array_slice($products, 0, 6);

// Get total number of products
$total_products = count($products);

// Page title
$page_title = "Home - " . SITE_NAME;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="message-container"></div>
        
        <!-- Hero Section -->
        <section class="hero">
            <div style="background-color: #007bff; color: white; padding: 50px 20px; text-align: center; border-radius: 5px; margin: 20px 0;">
                <h1>Welcome to <?php echo SITE_NAME; ?></h1>
                <p style="margin: 20px 0;">Find the best products at the best prices</p>
                <a href="products.php" class="btn" style="background-color: white; color: #007bff;">Shop Now</a>
            </div>
        </section>
        
        <!-- Featured Products -->
        <section>
            <h2>Featured Products</h2>
            <div class="products">
                <?php foreach ($featured_products as $product): ?>
                <div class="product-card">
                    <img src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="product-img">
                    <div class="product-info">
                        <h3 class="product-title"><?php echo $product['name']; ?></h3>
                        <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        
        <!-- Categories -->
        <section>
            <h2>Shop by Category</h2>
            <div style="display: flex; flex-wrap: wrap; gap: 20px; margin: 20px 0;">
                <a href="products.php?category=Electronics" style="flex: 1; min-width: 200px; text-align: center; padding: 20px; background-color: #f1f1f1; border-radius: 5px; text-decoration: none; color: #333;">
                    <h3>Electronics</h3>
                </a>
                <a href="products.php?category=Clothing" style="flex: 1; min-width: 200px; text-align: center; padding: 20px; background-color: #f1f1f1; border-radius: 5px; text-decoration: none; color: #333;">
                    <h3>Clothing</h3>
                </a>
                <a href="products.php?category=Footwear" style="flex: 1; min-width: 200px; text-align: center; padding: 20px; background-color: #f1f1f1; border-radius: 5px; text-decoration: none; color: #333;">
                    <h3>Footwear</h3>
                </a>
                <a href="products.php?category=Home" style="flex: 1; min-width: 200px; text-align: center; padding: 20px; background-color: #f1f1f1; border-radius: 5px; text-decoration: none; color: #333;">
                    <h3>Home</h3>
                </a>
            </div>
        </section>
        
        <!-- About Section -->
        <section style="margin: 40px 0; padding: 20px; background-color: #f9f9f9; border-radius: 5px;">
            <h2>About Us</h2>
            <p>Welcome to <?php echo SITE_NAME; ?>, your one-stop shop for all your needs. We offer a wide range of products at competitive prices with excellent customer service.</p>
            <p>Our store features <?php echo $total_products; ?> products across multiple categories, ensuring you find exactly what you're looking for.</p>
        </section>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
