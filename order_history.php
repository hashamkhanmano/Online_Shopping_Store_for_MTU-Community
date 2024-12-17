<?php
// Include database connection file
require_once 'db.php';  // Make sure the path is correct

// Start the session to check if the user is logged in
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

// Get customer ID from session
$customer_id = $_SESSION['customer_id'];

// Initialize an empty array for orders
$orders = [];

// Database query to fetch orders for the logged-in user
try {
    // Query to get all orders placed by the logged-in customer
    $sql = "SELECT * FROM Orders WHERE customer_id = ? ORDER BY order_date DESC";
    $stmt = executeQuery($sql, [$customer_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Fetch all orders
} catch (Exception $e) {
    // Handle errors (e.g., connection issues)
    echo "An error occurred while fetching orders: " . $e->getMessage();
    exit();
}

// Include header (your navbar)
include 'header.php';  // Include the same header as cart.php
?>


<div class="container mt-5">
    <h2>Order History</h2>
    
    <?php if (count($orders) > 0): ?>
        <!-- Displaying the orders in a table -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Order Time</th>
                    <th>Total Price</th>
                    <th>Payment Method</th>
                    <th>Shipping Address</th>
                    <th>Shipping Phone</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars(date('F j, Y', strtotime($order['order_date']))); ?></td>
                        <td><?php echo htmlspecialchars(date('g:i A', strtotime($order['order_time']))); ?></td>
                        <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($order['shipping_address']); ?></td>
                        <td><?php echo htmlspecialchars($order['shipping_phone']); ?></td>
                        <td><a href="order_details.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-info btn-sm">View Details</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <!-- Message if no orders are found -->
        <p>You haven't placed any orders yet.</p>
    <?php endif; ?>
</div>

<!-- Simple Footer -->
<footer class="text-center mt-5">
    <p>&copy; <?php echo date('Y'); ?> My Store. All Rights Reserved.</p>
</footer>

<?php
// No footer.php file included here, just a simple footer in this file
?>
