<?php
session_start();
require_once 'db.php';  // Include the database connection

// Ensure the cart is not empty
if (empty($_SESSION['cart'])) {
    echo "Your cart is empty.";
    exit();
}

// Ensure customer is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "Please log in to place an order.";
    exit();
}

// Function to check if stock is sufficient for each cart item
function validateStock($cartItems) {
    $errors = [];
    
    // Loop through each item in the cart
    foreach ($cartItems as $cart_item) {
        // Get the stock available for the product from the database
        $query = "SELECT stock_quantity FROM Product WHERE product_id = :product_id";
        $stmt = executeQuery($query, ['product_id' => $cart_item['product_id']]);
        $product = $stmt->fetch();
        
        // If the requested quantity exceeds the available stock, add an error
        if ($cart_item['quantity'] > $product['stock_quantity']) {
            $errors[] = "Sorry, there are only {$product['stock_quantity']} units of {$cart_item['name']} available. You currently have {$cart_item['quantity']} in your cart.";
        }
    }
    
    return $errors;
}

// Validate stock before checkout
$stockErrors = validateStock($_SESSION['cart']);

// If there are stock errors, display them and abort the checkout
if (!empty($stockErrors)) {
    echo "<div class='alert alert-danger text-center' style='margin-top: 50px; padding: 20px;'>";
    // Loop through each error and display it in bold
    foreach ($stockErrors as $error) {
        echo "<p><strong>{$error}</strong></p>";  // Bold the error message
    }
    // Add a message with a link back to the shopping cart
    echo "<p><strong>Please update your cart to proceed with checkout.</strong></p>";
    echo "<a href='cart.php' class='btn btn-secondary'>Back to Shopping Cart</a>";  // Link to the shopping cart page
    echo "</div>";
    
    // Stop further processing
    exit();
}

// Initialize the order total
$order_total = 0;

// Calculate the total price from the cart (updated quantities)
foreach ($_SESSION['cart'] as $cart_item) {
    $order_total += $cart_item['price'] * $cart_item['quantity'];
}

// Process the order when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture the shipping details and payment method
    $shipping_address = $_POST['shipping_address'];
    $shipping_phone = $_POST['shipping_phone'];
    $payment_method = $_POST['payment_method'];
    $customer_id = $_SESSION['customer_id'];  // Customer ID from session

    // Begin the transaction
    try {
        $pdo = connectDB();  // Get a database connection
        $pdo->beginTransaction();  // Start the transaction

        // Insert order into the Orders table
        $query = "INSERT INTO Orders (customer_id, total_price, shipping_address, shipping_phone, payment_method, order_time) 
                  VALUES (:customer_id, :total_price, :shipping_address, :shipping_phone, :payment_method, NOW())";
        $params = [
            ':customer_id' => $customer_id,
            ':total_price' => $order_total,
            ':shipping_address' => $shipping_address,
            ':shipping_phone' => $shipping_phone,
            ':payment_method' => $payment_method
        ];

        // Prepare and execute the query
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        // Get the last inserted order ID
        $order_id = $pdo->lastInsertId();

        // Insert the products into OrderDetails table
        foreach ($_SESSION['cart'] as $cart_item) {
            $query = "INSERT INTO OrderDetails (order_id, product_id, price, quantity) 
                      VALUES (:order_id, :product_id, :price, :quantity)";
            $params = [
                ':order_id' => $order_id,
                ':product_id' => $cart_item['product_id'],
                ':price' => $cart_item['price'],
                ':quantity' => $cart_item['quantity']
            ];
            $pdo->prepare($query)->execute($params);  // Execute the query

            // Update the stock quantity in the Product table after the order is placed
            $update_query = "UPDATE Product SET stock_quantity = stock_quantity - :quantity WHERE product_id = :product_id";
            $update_params = [
                ':quantity' => $cart_item['quantity'],
                ':product_id' => $cart_item['product_id']
            ];
            $pdo->prepare($update_query)->execute($update_params);  // Update the stock quantity
        }

        // Commit the transaction
        $pdo->commit();

        // Clear the cart after a successful order
        unset($_SESSION['cart']);

        // Redirect to the order success page
        header('Location: order_success.php?order_id=' . $order_id);
        exit();

    } catch (Exception $e) {
        // Rollback if there is an error
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'header.php'; // Include the header ?>

<div class="container mt-5">
    <h2>Checkout Summary</h2>

    <!-- Display the cart items as a summary -->
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Available Stock</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_price = 0;
            foreach ($_SESSION['cart'] as $cart_item):
                // Get the stock quantity for the current product from the database
                $query = "SELECT stock_quantity FROM Product WHERE product_id = :product_id";
                $stmt = executeQuery($query, ['product_id' => $cart_item['product_id']]);
                $product = $stmt->fetch();
                
                $total_item_price = $cart_item['price'] * $cart_item['quantity'];
                $total_price += $total_item_price;

                // Check if product is in stock
                $stock_status = ($product['stock_quantity'] > 0) ? "In Stock" : "Out of Stock";
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($cart_item['name']); ?></td>
                    <td>$<?php echo number_format($cart_item['price'], 2); ?></td>
                    <td><?php echo $cart_item['quantity']; ?></td>
                    <td>
                        <?php 
                        // Display the stock status
                        if ($product['stock_quantity'] > 0) {
                            echo "<span class='text-success'>{$stock_status}</span>";
                        } else {
                            echo "<span class='text-danger'>{$stock_status}</span>";
                        }
                        ?>
                    </td>
                    <td>$<?php echo number_format($total_item_price, 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4>Total Price: $<?php echo number_format($total_price, 2); ?></h4>

    <!-- Shipping and Payment Info Form -->
    <h5>Shipping Details</h5>
    <form action="checkout.php" method="POST">
        <div class="form-group">
            <label for="shipping_address">Address</label>
            <input type="text" class="form-control" id="shipping_address" name="shipping_address" required>
        </div>
        <div class="form-group">
            <label for="shipping_phone">Phone Number</label>
            <input type="text" class="form-control" id="shipping_phone" name="shipping_phone" required>
        </div>

        <h5>Payment Options</h5>
        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <select class="form-control" id="payment_method" name="payment_method" required>
                <option value="credit_card">Credit Card</option>
                <option value="paypal">PayPal</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Place Order</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
