<?php
// Include the database connection file
require_once 'db.php';

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Fetch the current price (old price) of the product
    $query = "SELECT price FROM Product WHERE product_id = :product_id";
    $stmt = executeQuery($query, ['product_id' => $product_id]);
    $product = $stmt->fetch();

    if ($product) {
        echo json_encode(['success' => true, 'old_price' => $product['price']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No product ID provided.']);
}
?>
