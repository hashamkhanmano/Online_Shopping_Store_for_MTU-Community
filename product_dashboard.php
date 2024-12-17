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

// Fetch the employee's name (assuming it's stored in the session)
$employee_username = isset($_SESSION['employee_username']) ? $_SESSION['employee_username'] : 'Employee';

// Fetch products from the database
$query = "SELECT product_id, name FROM Product";
$stmt = executeQuery($query);
$products = $stmt->fetchAll();

// Handle success or error messages from the restock action
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : null;
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : null;

// Clear session messages
unset($_SESSION['success']);
unset($_SESSION['error']);

// Logout functionality
if (isset($_GET['logout'])) {
    // Destroy the session to log out the user
    session_destroy();
    header('Location: login.php'); // Redirect to login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Product Management</title>
    <!-- Bootstrap for styling -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dashboard-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .logout-btn {
            margin-top: 10px;
            text-align: right;
        }
        .insert-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- Welcome note -->
        <div class="row">
            <div class="col-md-12">
                <h4>Welcome, <?php echo htmlspecialchars($employee_username); ?>!</h4>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="logout-btn">
            <a href="?logout=true" class="btn btn-danger">Logout</a>
        </div>

        <h2>Product Dashboard</h2>

        <!-- Success message -->
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <!-- Error message -->
        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Start of Dashboard Row -->
        <div class="row">
            <!-- Restock Product Section -->
            <div class="col-md-6">
                <div class="dashboard-section">
                    <h4>Restock Product</h4>
                    <form action="restock_product.php" method="POST">
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
                            <label for="new_stock_quantity">New Stock Quantity</label>
                            <input type="number" name="new_stock_quantity" id="new_stock_quantity" class="form-control" min="0" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Stock</button>
                    </form>
                </div>
            </div>

            <!-- Change Product Price Section -->
            <div class="col-md-6">
                <div class="dashboard-section">
                    <h4>Change Product Price</h4>
                    <form action="change_price.php" method="POST">
                        <div class="form-group">
                            <label for="product_id_price">Product</label>
                            <select name="product_id" id="product_id_price" class="form-control" required>
                                <option value="">Select a Product</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['product_id']; ?>">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="new_price">New Price</label>
                            <input type="number" name="new_price" id="new_price" class="form-control" step="0.01" min="0" required>
                        </div>

                        <button type="submit" class="btn btn-warning">Change Price</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Start of Another Dashboard Row -->
        <div class="row">
            <!-- View Stock History Section -->
            <div class="col-md-6">
                <div class="dashboard-section">
                    <h4>Stock History</h4>
                    <form action="view_stock_history.php" method="GET">
                        <div class="form-group">
                            <label for="product_id_history">Product</label>
                            <select name="product_id" id="product_id_history" class="form-control" required>
                                <option value="">Select a Product</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['product_id']; ?>">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-info">View Stock History</button>
                    </form>
                </div>
            </div>

            <!-- View Price History Section -->
            <div class="col-md-6">
                <div class="dashboard-section">
                    <h4>Price History</h4>
                    <form action="view_price_history.php" method="GET">
                        <div class="form-group">
                            <label for="product_id_price_history">Product</label>
                            <select name="product_id" id="product_id_price_history" class="form-control" required>
                                <option value="">Select a Product</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['product_id']; ?>">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-info">View Price History</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Insert New Product Button -->
        <div class="insert-btn text-center">
            <a href="insert_product.php" class="btn btn-success">Insert New Product</a>
        </div>

    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
