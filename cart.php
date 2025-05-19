<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php?redirect=cart.php');
    exit();
}

// Get cart items
$cart_items = getCartItems($_SESSION['user_id']);
$cart_total = getCartTotal($_SESSION['user_id']);

// Page title
$page_title = "Shopping Cart - " . SITE_NAME;
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
        
        <h1>Shopping Cart</h1>
        
        <div class="cart-container">
            <?php if (empty($cart_items)): ?>
                <p>Your cart is empty.</p>
                <a href="products.php" class="btn">Continue Shopping</a>
            <?php else: ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center;">
                                        <img src="assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                        <a href="product.php?id=<?php echo $item['product_id']; ?>"><?php echo $item['name']; ?></a>
                                    </div>
                                </td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <input type="number" class="cart-quantity form-control" data-cart-id="<?php echo $item['id']; ?>" value="<?php echo $item['quantity']; ?>" min="1" max="10" style="width: 60px;">
                                </td>
                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                <td>
                                    <button class="btn btn-danger remove-from-cart" data-cart-id="<?php echo $item['id']; ?>">Remove</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="cart-summary">
                    <div class="cart-total">
                        Total: <span class="cart-total-value">$<?php echo number_format($cart_total, 2); ?></span>
                    </div>
                    <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
