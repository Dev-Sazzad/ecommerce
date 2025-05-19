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
    header("Location: login.php?redirect=view_order.php" . (isset($_GET['id']) ? "?id=" . $_GET['id'] : ""));
    exit();
}

// Get the order ID from URL parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect to orders page if no order ID provided
    header("Location: orders.php");
    exit();
}

$order_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch the order details
$order_query = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

// Check if order exists and belongs to the logged-in user
if ($order_result->num_rows == 0) {
    // Order not found or doesn't belong to the user
    header("Location: orders.php?error=invalid_order");
    exit();
}

$order = $order_result->fetch_assoc();

// Fetch order items
$items_query = "SELECT oi.*, p.name, p.price, p.image_url 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
$stmt = $conn->prepare($items_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();

// Get shipping address
$address_query = "SELECT * FROM shipping_addresses WHERE id = ?";
$stmt = $conn->prepare($address_query);
$stmt->bind_param("i", $order['shipping_address_id']);
$stmt->execute();
$address_result = $stmt->get_result();
$shipping_address = $address_result->fetch_assoc();

// Get page title
$page_title = "Order #" . $order_id;

// Include header
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1>Order #<?php echo $order_id; ?></h1>
            <p class="lead">Placed on: <?php echo date("F j, Y, g:i a", strtotime($order['order_date'])); ?></p>
            
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Order Status: 
                                <span class="badge 
                                <?php 
                                    switch($order['status']) {
                                        case 'pending': echo 'bg-warning'; break;
                                        case 'processing': echo 'bg-info'; break;
                                        case 'shipped': echo 'bg-primary'; break;
                                        case 'delivered': echo 'bg-success'; break;
                                        case 'cancelled': echo 'bg-danger'; break;
                                        default: echo 'bg-secondary';
                                    }
                                ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </h5>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h5>Payment Method: <?php echo ucfirst($order['payment_method']); ?></h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Shipping Address</h5>
                            <?php if ($shipping_address): ?>
                                <address>
                                    <?php echo $shipping_address['full_name']; ?><br>
                                    <?php echo $shipping_address['address_line1']; ?><br>
                                    <?php if (!empty($shipping_address['address_line2'])): ?>
                                        <?php echo $shipping_address['address_line2']; ?><br>
                                    <?php endif; ?>
                                    <?php echo $shipping_address['city']; ?>, 
                                    <?php echo $shipping_address['state']; ?> 
                                    <?php echo $shipping_address['postal_code']; ?><br>
                                    <?php echo $shipping_address['country']; ?>
                                </address>
                            <?php else: ?>
                                <p>Shipping address not available</p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h5>Order Summary</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td>Subtotal:</td>
                                    <td class="text-end"><?php echo format_currency($order['subtotal']); ?></td>
                                </tr>
                                <?php if ($order['discount'] > 0): ?>
                                <tr>
                                    <td>Discount:</td>
                                    <td class="text-end">-<?php echo format_currency($order['discount']); ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td>Shipping:</td>
                                    <td class="text-end"><?php echo format_currency($order['shipping_cost']); ?></td>
                                </tr>
                                <tr>
                                    <td>Tax:</td>
                                    <td class="text-end"><?php echo format_currency($order['tax']); ?></td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>Total:</td>
                                    <td class="text-end"><?php echo format_currency($order['total_amount']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <h3>Order Items</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $items_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($item['image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-thumbnail me-3" style="width: 60px;">
                                        <?php endif; ?>
                                        <div>
                                            <a href="product.php?id=<?php echo $item['product_id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a>
                                            <?php if (!empty($item['options'])): ?>
                                                <small class="text-muted d-block">
                                                    <?php 
                                                        $options = json_decode($item['options'], true);
                                                        if (is_array($options)) {
                                                            foreach ($options as $key => $value) {
                                                                echo htmlspecialchars(ucfirst($key)) . ': ' . htmlspecialchars($value) . '<br>';
                                                            }
                                                        }
                                                    ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo format_currency($item['price']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td class="text-end"><?php echo format_currency($item['price'] * $item['quantity']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
                
                <?php if ($order['status'] !== 'cancelled'): ?>
                    <?php if ($order['status'] === 'pending' || $order['status'] === 'processing'): ?>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                            Cancel Order
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<?php if ($order['status'] === 'pending' || $order['status'] === 'processing'): ?>
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this order? This action cannot be undone.</p>
                <form id="cancelOrderForm" action="cancel_order.php" method="post">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                    <div class="mb-3">
                        <label for="cancelReason" class="form-label">Reason for cancellation (optional):</label>
                        <textarea class="form-control" id="cancelReason" name="cancel_reason" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="cancelOrderForm" class="btn btn-danger">Cancel Order</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
// Include footer
include 'includes/footer.php';
?>
