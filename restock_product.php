<?php
// Start the session to check login status
session_start();

// Include the database connection file
require_once 'db.php';

// Ensure the user is an employee
if (!isset($_SESSION['employee_id'])) {
    // Redirect to login page if the user is not an employee
    header('Location: login.php'); 
    exit();
}

// Check if the form was submitted (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the product ID and new stock quantity from the form
    $product_id = $_POST['product_id'];
    $new_stock_quantity = $_POST['new_stock_quantity'];

    // Validate stock quantity
    if (empty($new_stock_quantity) || !is_numeric($new_stock_quantity) || $new_stock_quantity < 0) {
        $_SESSION['error'] = "Invalid stock quantity. Please enter a valid number greater than or equal to 0.";
        header('Location: product_dashboard.php'); // Redirect back to dashboard or product page
        exit();
    }

    // Fetch the current stock quantity from the Product table
    $query = "SELECT stock_quantity FROM Product WHERE product_id = :product_id";
    $stmt = executeQuery($query, ['product_id' => $product_id]);
    $current_stock_quantity = $stmt->fetchColumn();

    if ($current_stock_quantity === false) {
        // If the product doesn't exist, show an error
        $_SESSION['error'] = "Product not found.";
        header('Location: product_dashboard.php');
        exit();
    }

    // Record the old stock quantity
    $old_stock_quantity = $current_stock_quantity;

    // Update the stock quantity in the Product table
    $update_query = "UPDATE Product SET stock_quantity = :new_stock_quantity WHERE product_id = :product_id";
    $stmt = executeQuery($update_query, ['new_stock_quantity' => $new_stock_quantity, 'product_id' => $product_id]);

    // Check if the update was successful
    if ($stmt->rowCount() > 0) {
        // Log the stock change in the StockHistory table
        $change_reason = "Restock"; // You can change this to any reason you want
        $history_query = "INSERT INTO StockHistory (product_id, old_stock_quantity, new_stock_quantity, change_reason) 
                          VALUES (:product_id, :old_stock_quantity, :new_stock_quantity, :change_reason)";
        executeQuery($history_query, [
            'product_id' => $product_id,
            'old_stock_quantity' => $old_stock_quantity,
            'new_stock_quantity' => $new_stock_quantity,
            'change_reason' => $change_reason
        ]);

        // Provide feedback on success
        $_SESSION['success'] = "Stock updated successfully!";
        header('Location: product_dashboard.php'); // Redirect to the product dashboard after success
        exit();
    } else {
        $_SESSION['error'] = "Failed to update stock. Please try again.";
        header('Location: product_dashboard.php'); // Redirect to retry page if update fails
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request method. Please use the form to update stock.";
    header('Location: product_dashboard.php'); // Redirect to the dashboard if not a POST request
    exit();
}
?>
