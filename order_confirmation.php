<?php
session_start();

// Retrieve order ID from query parameters
$order_id = $_GET['order_id'] ?? 0;

// Fetch order details from the database
$query = "SELECT * FROM Orders WHERE order_id = :order_id";
$stmt = executeQuery($query, ['order_id' => $order_id]);
$order = $stmt->fetch();

// Fetch order products from the OrderDetails table
$query = "SELECT * FROM OrderDetails WHERE order_id = :order_id";
$stmt = executeQuery($query, ['order_id' => $order_id]);
$order_details = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'header.php';  // Include header ?>

<div class="container mt-5">
    <h2>Order Confirmation</h2>
    
    <h4>Order #<?php echo $order['order_id']; ?></h4>
    <p>Order Date: <?php echo $order['order_date']; ?></p>
    <p>Total: $<?php echo number_format($order['total_price'], 2); ?></p>

    <h5>Order Details</h5>
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order_details as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="cust_main.php" class="btn btn-primary">Continue Shopping</a>
</div>

</body>
</html>
