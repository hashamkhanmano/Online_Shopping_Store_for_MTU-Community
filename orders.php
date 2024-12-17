<?php
// Start the session to check login status
session_start();

// Include the database connection file
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    // If not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}

// Fetch the customer ID from the session
$customer_id = $_SESSION['customer_id'];

// Query to fetch all orders for the logged-in customer
$query = "SELECT * FROM Orders WHERE customer_id = :customer_id ORDER BY order_date DESC";
$stmt = executeQuery($query, ['customer_id' => $customer_id]);
$orders = $stmt->fetchAll();

// If no orders are found, display a message
if (empty($orders)) {
    echo "<h2>No orders found.</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Your Orders</h2>

    <!-- Loop through the orders and display them -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Order Date</th>
                <th>Total</th>
                <th>View Details</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo $order['order_id']; ?></td>
                    <td><?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></td>
                    <td>$<?php echo number_format($order['total'], 2); ?></td>
                    <td><a href="order_details.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-info">View Details</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
