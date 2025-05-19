<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

// Get cart items
$cart_items = getCartItems($_SESSION['user_id']);
$cart_total = getCartTotal($_SESSION['user_id']);

// Redirect if cart is empty
if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}

$errors = [];
$order_id = null;

// Process checkout form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $zip = trim($_POST['zip'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';
    
    // Validate inputs
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email";
    }
    
    if (empty($address)) {
        $errors[] = "Address is required";
    }
    
    if (empty($city)) {
        $errors[] = "City is required";
    }
    
    if (empty($zip)) {
        $errors[] = "ZIP is required";
    }
    
    if (empty($payment_method)) {
        $errors[] = "Payment method is required";
    }
    
    // If no errors, create order
    if (empty($errors)) {
        // In a real application, you would process payment here
        
        // Create order in database
        $order_id = createOrder($_SESSION['user_id'], $cart_total);
        
        if ($order_id) {
            // Store shipping details (not implemented in this simple version)
            // In a real application, you would save shipping details to a shipping_details table
            
            // Redirect to order confirmation page
            header("Location: order_confirmation.php?order_id=$order_id");
            exit();
        } else {
            $errors[] = "Failed to create order. Please try again.";
        }
    }
}

// Page title
$page_title = "Checkout - " . SITE_NAME;
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
        <div class="message-container">
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="message error"><?php echo $error; ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <h1>Checkout</h1>
        
        <div style="display: flex; flex-wrap: wrap; gap: 20px; margin: 30px 0;">
            <!-- Order Summary -->
            <div style="flex: 1; min-width: 300px;">
                <div style="background-color: #f9f9f9; padding: 20px; border-radius: 5px;">
                    <h2>Order Summary</h2>
                    <div style="margin: 15px 0; border-bottom: 1px solid #ddd;"></div>
                    
                    <?php foreach ($cart_items as $item): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <div>
                                <?php echo $item['name']; ?> x <?php echo $item['quantity']; ?>
                            </div>
                            <div>
                                $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="margin: 15px 0; border-bottom: 1px solid #ddd;"></div>
                    
                    <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 18px;">
                        <div>Total:</div>
                        <div>$<?php echo number_format($cart_total, 2); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Checkout Form -->
            <div style="flex: 2; min-width: 300px;">
                <form method="POST" action="">
                    <h2>Shipping Information</h2>
                    
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" class="form-control" required>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" class="form-control" required>
                        </div>
                        
                        <div class="form-group" style="flex: 1;">
                            <label for="zip">ZIP Code</label>
                            <input type="text" id="zip" name="zip" class="form-control" required>
                        </div>
                    </div>
                    
                    <h2 style="margin-top: 20px;">Payment Method</h2>
                    
                    <div class="form-group">
                        <label>
                            <input type="radio" name="payment_method" value="credit_card" checked> Credit Card
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="radio" name="payment_method" value="paypal"> PayPal
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="radio" name="payment_method" value="cash_on_delivery"> Cash on Delivery
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Place Order</button>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
