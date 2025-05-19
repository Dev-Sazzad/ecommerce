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
    $response['message'] = 'You must be logged in to update cart';
    echo json_encode($response);
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get cart ID and quantity
    $cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Validate inputs
    if ($cart_id <= 0) {
        $response['message'] = 'Invalid cart item';
        echo json_encode($response);
        exit();
    }
    
    if ($quantity <= 0) {
        $quantity = 1; // Minimum quantity is 1
    }
    
    // Update cart item in database
    global $conn;
    $query = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $quantity, $cart_id, $_SESSION['user_id']);
    $result = $stmt->execute();
    
    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Cart updated successfully';
        $response['total'] = getCartTotal($_SESSION['user_id']);
    } else {
        $response['message'] = 'Failed to update cart';
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
