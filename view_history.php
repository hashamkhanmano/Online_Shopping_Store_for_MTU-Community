<?php
// Start the session to check login status
session_start();

// Include the database connection file
require_once 'db.php';

// Ensure the user is an employee
if (!isset($_SESSION['employee_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in as employee
    exit();
}

// Fetch the product_id from the URL
$product_id = $_GET['product_id'] ?? 0;
if ($product_id <= 0) {
    die("Invalid product ID.");
}

// Fetch product details
$productQuery = "SELECT * FROM Product WHERE product_id = :product_id";
$productStmt = executeQuery($productQuery, ['product_id' => $product_id]);
$product = $productStmt->fetch();

// If product is not found
if (!$product) {
    die("Product not found.");
}

// Fetch the product's price history
$priceHistoryQuery = "SELECT * FROM ProductPriceHistory WHERE product_id = :product_id ORDER BY change_time DESC";
$priceHistoryStmt = executeQuery($priceHistoryQuery, ['product_id' => $product_id]);
$priceHistory = $priceHistoryStmt->fetchAll();

// Optionally, you could also fetch stock history if available
// $stockHistoryQuery = "SELECT * FROM StockHistory WHERE product_id = :product_id ORDER BY change_time DESC";
// $stockHistoryStmt = executeQuery($stockHistoryQuery, ['product_id' => $product_id]);
// $stockHistory = $stockHistoryStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product History</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Product History - <?= htmlspecialchars($product['name']) ?></h2>

    <h4>Price History</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Old Price</th>
                <th>New Price</th>
                <th>Change Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($priceHistory as $history): ?>
                <tr>
                    <td>$<?= number_format($history['old_price'], 2) ?></td>
                    <td>$<?= number_format($history['new_price'], 2) ?></td>
                    <td><?= $history['change_time'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Optional: You could also show stock history in a similar format -->
    <!-- <h4>Stock History</h4> -->
    <!-- <table class="table table-bordered"> -->
    <!--     <thead> -->
    <!--         <tr> -->
    <!--             <th>Old Stock</th> -->
    <!--             <th>New Stock</th> -->
    <!--             <th>Change Time</th> -->
    <!--         </tr> -->
    <!--     </thead> -->
    <!--     <tbody> -->
    <!--         <?php foreach ($stockHistory as $history): ?> -->
    <!--             <tr> -->
    <!--                 <td><?= $history['old_stock'] ?></td> -->
    <!--                 <td><?= $history['new_stock'] ?></td> -->
    <!--                 <td><?= $history['change_time'] ?></td> -->
    <!--             </tr> -->
    <!--         <?php endforeach; ?> -->
    <!--     </tbody> -->
    <!-- </table> -->

</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
