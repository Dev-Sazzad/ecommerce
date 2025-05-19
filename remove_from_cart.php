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
    $response['message'] = 'You must be logged in to remove items from cart';
    echo json_encode($response);
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get cart ID
    $cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
    
    // Validate inputs
    if ($cart_id <= 0) {
        $response['message'] = 'Invalid cart item';
        echo json_encode($response);
        exit();
    }
    
    // Remove from cart
    $result = removeFromCart($cart_id, $_SESSION['user_id']);
    
    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Item removed from cart successfully';
        $response['total'] = getCartTotal($_SESSION['user_id']);
    } else {
        $response['message'] = 'Failed to remove item from cart';
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
