<?php
// Start the session to access order_id passed in the URL
session_start();

// Get the order_id from the URL
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if ($order_id) {
    // In a real application, you would retrieve the order details from the database.
    // For now, we're just displaying the order_id as a placeholder for the order success message.
    // Example: Fetch the customer name and other details (order date, total price, etc.).
    $customer_name = "John Doe";  // This should come from the session or database

    // Let's assume the shipping address and other details are retrieved here:
    $shipping_address = "123 Main St, City, State, ZIP"; // Example address
    $delivery_time = "2-5 days"; // Estimated delivery time
} else {
    $order_id = null;
    echo "<h1>Order Error</h1>";
    echo "<p>Invalid order ID.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - My Store</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Header Section -->
<?php include 'header.php';  // Include the header ?>

<div class="container mt-5">
    <h2 class="text-center">Order Success</h2>
    
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading">Thank you for your order!</h4>
        <p>Your order has been successfully placed.</p>
        <hr>
        <p class="mb-0">Order ID: <strong><?php echo htmlspecialchars($order_id); ?></strong></p>
        <p>Your order will be delivered to your shipping address in approximately <strong>2-5 days</strong>.</p>
        <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($shipping_address); ?></p>
    </div>

    <div class="mt-4 text-center">
        <a href="cust_main.php" class="btn btn-primary">Continue Shopping</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

</body>
</html>
