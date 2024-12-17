<?php
session_start();  // Start the session

// Ensure the user is logged in before adding to cart
if (!isset($_SESSION['customer_id'])) {
    // If not logged in, redirect to login page
    header('Location: login.php');
    exit();
}

// Include database connection file
require_once 'db.php';

// Get product details from the query parameters
$product_id = $_GET['product_id'] ?? null;
$product_name = $_GET['product_name'] ?? '';
$product_price = $_GET['product_price'] ?? 0;
$product_image = $_GET['product_image'] ?? '';  // Add the product image URL

// Debugging: Check if the parameters are being passed
if (empty($product_name) || $product_price <= 0 || $product_id === null || empty($product_image)) {
    echo "Invalid product details.<br>";
    echo "Product Name: " . htmlspecialchars($product_name) . "<br>";
    echo "Product Price: " . htmlspecialchars($product_price) . "<br>";
    echo "Product ID: " . htmlspecialchars($product_id) . "<br>";
    echo "Product Image: " . htmlspecialchars($product_image) . "<br>";
    exit();  // Stop execution if parameters are missing
}

// Get the customer_id from session
$customer_id = $_SESSION['customer_id'];

// Initialize the cart in session if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if the product already exists in the Cart table
$query = "SELECT * FROM Cart WHERE customer_id = :customer_id AND product_id = :product_id";
$stmt = executeQuery($query, ['customer_id' => $customer_id, 'product_id' => $product_id]);

// Fetch the result
$cart_item = $stmt->fetch();

if ($cart_item) {
    // If product exists in the cart, update the quantity in the database
    $new_quantity = $cart_item['quantity'] + 1;
    $update_query = "UPDATE Cart SET quantity = :quantity WHERE cart_id = :cart_id";
    executeQuery($update_query, ['quantity' => $new_quantity, 'cart_id' => $cart_item['cart_id']]);
    
    // Also update the session cart
    foreach ($_SESSION['cart'] as &$session_item) {
        if ($session_item['product_id'] == $product_id) {
            $session_item['quantity'] += 1;  // Increase quantity if item already in the session cart
            break;
        }
    }
} else {
    // If the product doesn't exist in the cart, add it to the Cart table
    $insert_query = "INSERT INTO Cart (customer_id, product_id, quantity) VALUES (:customer_id, :product_id, :quantity)";
    executeQuery($insert_query, ['customer_id' => $customer_id, 'product_id' => $product_id, 'quantity' => 1]);
    
    // Also add the item to the session cart
    $item = [
        'product_id' => $product_id,
        'name' => $product_name,
        'price' => $product_price,
        'quantity' => 1,
        'image' => $product_image  // Store the product image URL
    ];

    $_SESSION['cart'][] = $item;
}

// Redirect to the cart page
header('Location: cart.php');
exit();
?>
