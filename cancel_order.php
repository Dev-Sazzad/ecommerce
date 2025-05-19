<?php
// Start session if not already started
session_start();

// Include necessary files
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redirect to orders page if not a POST request
    header("Location: orders.php");
    exit();
}

// Get form data
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$cancel_reason = isset($_POST['cancel_reason']) ? trim($_POST['cancel_reason']) : '';
$user_id = $_SESSION['user_id'];

// Validate order ID
if ($order_id <= 0) {
    // Invalid order ID
    header("Location: orders.php?error=invalid_order");
    exit();
}

// Check if the order exists and belongs to the user
$check_query = "SELECT * FROM orders WHERE id = ? AND user_id = ? AND (status = 'pending' OR status = 'processing')";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if order exists and is cancellable
if ($result->num_rows == 0) {
    // Order not found or doesn't belong to the user or is already past cancellation stage
    header("Location: orders.php?error=cannot_cancel");
    exit();
}

// Begin transaction
$conn->begin_transaction();

try {
    // Update order status
    $update_query = "UPDATE orders SET status = 'cancelled', updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    // Log the cancellation reason if provided
    if (!empty($cancel_reason)) {
        $log_query = "INSERT INTO order_logs (order_id, action, message, created_at) VALUES (?, 'cancel', ?, NOW())";
        $stmt = $conn->prepare($log_query);
        $stmt->bind_param("is", $order_id, $cancel_reason);
        $stmt->execute();
    }

    // If using inventory management, update the stock levels
    $inventory_query = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($inventory_query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $inventory_result = $stmt->get_result();
    
    while ($item = $inventory_result->fetch_assoc()) {
        $update_stock = "UPDATE products SET stock = stock + ? WHERE id = ?";
        $stmt = $conn->prepare($update_stock);
        $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
        $stmt->execute();
    }
    
    // Commit transaction
    $conn->commit();
    
    // Redirect to view order page with success message
    header("Location: view_order.php?id=$order_id&message=order_cancelled");
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    // Redirect to view order page with error message
    header("Location: view_order.php?id=$order_id&error=cancel_failed");
}

// Close connection
$conn->close();
?>

