<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php?redirect=orders.php');
    exit();
}

// Get user orders
$user_id = $_SESSION['user_id'];
$orders = getUserOrders($user_id);

// Page title
$page_title = "My Orders - " . SITE_NAME;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .orders-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .order-card {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .order-status {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-processing {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-shipped {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .order-items {
            margin-bottom: 15px;
        }
        
        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .empty-orders {
            text-align: center;
            padding: 50px 0;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <h1>My Orders</h1>
        
        <div class="orders-container">
            <?php if (empty($orders)): ?>
                <div class="empty-orders">
                    <h2>You haven't placed any orders yet</h2>
                    <p>Browse our products and make your first purchase!</p>
                    <a href="products.php" class="btn">Shop Now</a>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <h3>Order #<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?></h3>
                                <p>Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                            </div>
                            <div>
                                <span class="order-status status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
                            </div>
                        </div>
                        
                        <div class="order-items">
                            <?php
                            // Get order items
                            global $conn;
                            $items_query = "SELECT i.*, p.name, p.image FROM order_items i
                                          JOIN products p ON i.product_id = p.id
                                          WHERE i.order_id = ?";
                            $items_stmt = $conn->prepare($items_query);
                            $items_stmt->bind_param("i", $order['id']);
                            $items_stmt->execute();
                            $items_result = $items_stmt->get_result();
                            
                            $item_count = $items_result->num_rows;
                            $first_item = $items_result->fetch_assoc();
                            
                            if ($first_item):
                            ?>
                                <div class="order-item">
                                    <img src="assets/images/<?php echo $first_item['image']; ?>" alt="<?php echo $first_item['name']; ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                    <span><?php echo $first_item['name']; ?></span>
                                    <?php if ($item_count > 1): ?>
                                        <span>and <?php echo $item_count - 1; ?> more item(s)</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="order-footer">
                            <div class="order-total">
                                <strong>Total:</strong> $<?php echo number_format($order['total'], 2); ?>
                            </div>
                            <div class="order-actions">
                                <a href="view_order.php?id=<?php echo $order['id']; ?>" class="btn">View Order</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
