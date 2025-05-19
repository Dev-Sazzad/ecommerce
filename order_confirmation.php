<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Get order details from database
global $conn;
$query = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// If order not found, redirect to home
if ($result->num_rows === 0) {
    header('Location: index.php');
    exit();
}

$order = $result->fetch_assoc();

// Get order items
$items_query = "SELECT oi.*, p.name, p.image FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_query);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

$order_items = array();
while ($item = $items_result->fetch_assoc()) {
    $order_items[] = $item;
}

// Page title
$page_title = "Order Confirmation - " . SITE_NAME;
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
        <div style="max-width: 800px; margin: 30px auto; background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
            <div style="text-align: center; color: green; margin-bottom: 20px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                </svg>
                <h1>Thank You for Your Order!</h1>
                <p>Your order has been placed successfully.</p>
            </div>
            
            <div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <h2>Order Details</h2>
                <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
                <p><strong>Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></p>
                <p><strong>Status:</strong> <span style="text-transform: capitalize;"><?php echo $order['status']; ?></span></p>
                <p><strong>Total:</strong> $<?php echo number_format($order['total'], 2); ?></p>
            </div>
            
            <h2>Order Items</h2>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <img src="assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                    <?php echo $item['name']; ?>
                                </div>
                            </td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 30px; text-align: center;">
                <a href="index.php" class="btn">Continue Shopping</a>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
