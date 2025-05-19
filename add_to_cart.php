<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Initialize response array
$response = array(
    'success' => false,
    'message' => '',
    'total' => 0
);

// Check if user is logged in
if (!isLoggedIn()) {
    $response['message'] = 'You must be logged in to add items to cart';
    echo json_encode($response);
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get product ID and quantity
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Validate inputs
    if ($product_id <= 0) {
        $response['message'] = 'Invalid product';
        echo json_encode($response);
        exit();
    }
    
    if ($quantity <= 0 || $quantity > 10) {
        $quantity = 1; // Default to 1 if invalid
    }
    
    // Add to cart
    $result = addToCart($_SESSION['user_id'], $product_id, $quantity);
    
    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Product added to cart successfully';
        $response['total'] = getCartTotal($_SESSION['user_id']);
    } else {
        $response['message'] = 'Failed to add product to cart';
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>

