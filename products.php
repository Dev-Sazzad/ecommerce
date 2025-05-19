<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get products based on category or search
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

if (!empty($category)) {
    $products = getProductsByCategory($category);
    $page_title = "$category - " . SITE_NAME;
} elseif (!empty($search)) {
    // Search functionality would need to be implemented in functions.php
    // For simplicity, we'll just display all products for now
    $products = getAllProducts();
    $page_title = "Search Results for '$search' - " . SITE_NAME;
} else {
    $products = getAllProducts();
    $page_title = "All Products - " . SITE_NAME;
}
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
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
            <h1>
                <?php if (!empty($category)): ?>
                    <?php echo $category; ?>
                <?php elseif (!empty($search)): ?>
                    Search Results for '<?php echo htmlspecialchars($search); ?>'
                <?php else: ?>
                    All Products
                <?php endif; ?>
            </h1>
            
            <form id="search-form" action="products.php" method="GET">
                <div style="display: flex;">
                    <input type="text" id="search" name="search" placeholder="Search products..." style="padding: 8px; border: 1px solid #ddd; border-radius: 4px 0 0 4px;">
                    <button type="submit" class="btn" style="border-radius: 0 4px 4px 0;">Search</button>
                </div>
            </form>
        </div>
        
        <?php if (empty($products)): ?>
            <p>No products found.</p>
        <?php else: ?>
            <div class="products">
                <?php foreach ($products as $product): ?>
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
        <?php endif; ?>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
