<?php
// Start the session to check login status
session_start();

// Include the database connection file
require_once 'db.php';

// Ensure the user is logged in as an employee
if (!isset($_SESSION['employee_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in as employee
    exit();
}

// Fetch products from the database to populate the dropdown
$query = "SELECT product_id, name FROM Product";
$stmt = executeQuery($query);
$products = $stmt->fetchAll();

// Handle form submission for updating price
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the product ID and price change details
    $product_id = $_POST['product_id'];
    $old_price = $_POST['old_price'];
    $new_price = $_POST['new_price'];
    $change_reason = $_POST['change_reason'];

    // Validate price inputs
    if (empty($new_price) || !is_numeric($new_price) || $new_price < 0) {
        $_SESSION['error'] = "Invalid new price. Please enter a valid number greater than or equal to 0.";
        header('Location: pricehistory.php'); // Redirect back to the page
        exit();
    }

    // Insert the new price change into the ProductPriceHistory table
    $query = "INSERT INTO ProductPriceHistory (product_id, old_price, new_price, change_reason)
              VALUES (:product_id, :old_price, :new_price, :change_reason)";
    $stmt = executeQuery($query, [
        'product_id' => $product_id,
        'old_price' => $old_price,
        'new_price' => $new_price,
        'change_reason' => $change_reason
    ]);

    // Check if the insert was successful
    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "Price updated successfully!";
        header('Location: pricehistory.php'); // Redirect back after success
        exit();
    } else {
        $_SESSION['error'] = "Failed to update price. Please try again.";
        header('Location: pricehistory.php'); // Redirect back after failure
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price History - Product Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; // Assuming you have a header file ?>

    <div class="container mt-4">
        <h2>Price History</h2>

        <!-- Success message -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; ?>
            </div>
        <?php unset($_SESSION['success']); endif; ?>

        <!-- Error message -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; ?>
            </div>
        <?php unset($_SESSION['error']); endif; ?>

        <!-- Price Change Form -->
        <form action="pricehistory.php" method="POST">
            <div class="form-group">
                <label for="product_id">Product</label>
                <select name="product_id" id="product_id" class="form-control" required>
                    <option value="">Select a Product</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['product_id']; ?>">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="old_price">Old Price</label>
                <input type="number" name="old_price" id="old_price" class="form-control" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="new_price">New Price</label>
                <input type="number" name="new_price" id="new_price" class="form-control" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="change_reason">Change Reason</label>
                <textarea name="change_reason" id="change_reason" class="form-control" required></textarea>
            </div>

            <button type="submit" class="btn btn-warning">Update Price</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
