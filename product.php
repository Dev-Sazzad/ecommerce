<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get product by ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = getProductById($product_id);

// If product not found, redirect to products page
if (!$product) {
    header('Location: products.php');
    exit();
}

// Page title
$page_title = $product['name'] . " - " . SITE_NAME;
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
        
        <div class="product-details">
            <div class="product-details-img">
                <img src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
            </div>
            
            <div class="product-details-info">
                <h1 class="product-details-title"><?php echo $product['name']; ?></h1>
                <p class="product-details-price">$<?php echo number_format($product['price'], 2); ?></p>
                
                <?php if (!empty($product['category'])): ?>
                    <p><strong>Category:</strong> <a href="products.php?category=<?php echo urlencode($product['category']); ?>"><?php echo $product['category']; ?></a></p>
                <?php endif; ?>
                
                <div class="product-details-desc">
                    <p><?php echo nl2br($product['description']); ?></p>
                </div>
                
                <!-- Add to Cart Form -->
                <?php if (isLoggedIn()): ?>
                    <form class="add-to-cart-form" action="add_to_cart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        
                        <div class="form-group" style="max-width: 100px;">
                            <label for="quantity">Quantity</label>
                            <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="10">
                        </div>
                        
                        <button type="submit" class="btn btn-success" style="margin-top: 10px;">Add to Cart</button>
                    </form>
                <?php else: ?>
                    <p><a href="login.php?redirect=product.php?id=<?php echo $product['id']; ?>">Login</a> to add this product to your cart.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
</body>
</html>

