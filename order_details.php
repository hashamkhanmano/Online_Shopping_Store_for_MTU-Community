<?php 
// Start session to check if the user is logged in
session_start();

// Include database connection functions from db.php
require_once 'db.php';  // This will give us access to connectDB() and executeQuery()

// Redirect to login page if user is not logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

// Get the order ID from the URL
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Query to get the order details based on the order_id and customer_id
    $sql = "SELECT * FROM Orders WHERE order_id = ? AND customer_id = ?";
    try {
        $stmt = executeQuery($sql, [$order_id, $_SESSION['customer_id']]);  // Use executeQuery from db.php
        $order = $stmt->fetch();

        // If the order doesn't exist or doesn't belong to the current user, redirect
        if (!$order) {
            header("Location: order_history.php");
            exit();
        }

        // Query to get the order items (product details)
        $sql_items = "SELECT od.order_id, od.product_id, od.quantity, od.price, p.name AS product_name, p.image
                      FROM OrderDetails od
                      JOIN Product p ON od.product_id = p.product_id
                      WHERE od.order_id = ?";
        $stmt_items = executeQuery($sql_items, [$order_id]);
        $order_items = $stmt_items->fetchAll();
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }

} else {
    // If no order_id is provided, redirect to order history
    header("Location: order_history.php");
    exit();
}

// Include header (your navbar)
include 'header.php';  // Include the same header as cart.php
?>

<!-- Start Order Details Page -->
<div class="container mt-5">

    <!-- Order Information Section -->
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-lg border-light">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Order Details - Order ID: <?php echo htmlspecialchars($order['order_id']); ?></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h5>Order Information</h5>
                            <ul class="list-unstyled">
                                <li><strong>Date:</strong> <?php echo htmlspecialchars(date('F j, Y', strtotime($order['order_date']))); ?></li>
                                <li><strong>Time:</strong> <?php echo htmlspecialchars(date('g:i A', strtotime($order['order_time']))); ?></li>
                                <li><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></li>
                                <li><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h5>Shipping Information</h5>
                            <ul class="list-unstyled">
                                <li><strong>Address:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></li>
                                <li><strong>Phone:</strong> <?php echo htmlspecialchars($order['shipping_phone']); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items Section -->
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-lg border-light">
                <div class="card-header bg-secondary text-white text-center">
                    <h5>Order Items</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($order_items)): ?>
                        <p class="alert alert-warning">No items found for this order.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                                            <td>
                                                <img src="images/<?php echo htmlspecialchars($item['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                                     class="img-fluid" width="80" />
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Order History Button -->
    <div class="text-center">
        <a href="order_history.php" class="btn btn-primary btn-lg">Back to Order History</a>
    </div>

</div>
<!-- End Order Details Page -->

<?php
// Include footer (adjust path if necessary)
include('footer.php');
?>
