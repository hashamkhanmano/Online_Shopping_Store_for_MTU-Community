<?php
// Start the session
session_start();

// Include the database connection file
require_once 'db.php';

// Ensure the user is logged in as an employee
if (!isset($_SESSION['employee_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in as employee
    exit();
}

// Handle the price change action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the product ID and new price from the form
    $product_id = $_POST['product_id'];
    $new_price = $_POST['new_price'];
    
    // Fetch the current price of the product (old price) from the database
    $query = "SELECT price FROM Product WHERE product_id = :product_id";
    $stmt = executeQuery($query, ['product_id' => $product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        $_SESSION['error'] = 'Product not found.';
        header('Location: product_dashboard.php');
        exit();
    }

    $old_price = $product['price'];

    // Validate the new price (you can add more validation)
    if (!is_numeric($new_price) || $new_price < 0) {
        $_SESSION['error'] = 'Invalid price.';
        header('Location: product_dashboard.php');
        exit();
    }

    // Update the product price in the database
    $updateQuery = "UPDATE Product SET price = :new_price WHERE product_id = :product_id";
    executeQuery($updateQuery, ['new_price' => $new_price, 'product_id' => $product_id]);

    // Insert a record into the price history table
    $insertHistoryQuery = "INSERT INTO ProductPriceHistory (product_id, old_price, new_price) VALUES (:product_id, :old_price, :new_price)";
    executeQuery($insertHistoryQuery, [
        'product_id' => $product_id,
        'old_price' => $old_price,
        'new_price' => $new_price
    ]);

    // Set a success message and redirect
    $_SESSION['success'] = 'Price updated successfully.';
    header('Location: product_dashboard.php');
    exit();
}
?>
