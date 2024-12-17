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

// Fetch the employee's name (username) from the session
$employee_username = isset($_SESSION['employee_username']) ? $_SESSION['employee_username'] : 'Employee';

// Fetch products from the database to populate the dropdown
$query = "SELECT product_id, name FROM Product";
$stmt = executeQuery($query);
$products = $stmt->fetchAll();

// If a product_id is selected, fetch its price history
$price_history = [];
if (isset($_GET['product_id']) && !empty($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Fetch price history for the selected product
    $query = "SELECT ph.history_id, p.name AS product_name, ph.old_price, ph.new_price, ph.change_date, ph.change_reason
              FROM ProductPriceHistory ph
              JOIN Product p ON ph.product_id = p.product_id
              WHERE ph.product_id = :product_id
              ORDER BY ph.change_date DESC";
    $stmt = executeQuery($query, ['product_id' => $product_id]);
    $price_history = $stmt->fetchAll();
}

// Handle logout functionality
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
    <title>Price History - Product Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .logout-btn {
            margin-top: 10px;
            text-align: right;
        }
        .home-btn {
            margin-top: 10px;
            text-align: left;
        }
    </style>
</head>
<body>

    <div class="container mt-4">
        <!-- Welcome Note -->
        <div class="row">
            <div class="col-md-12">
                <h4>Welcome, <?php echo htmlspecialchars($employee_username); ?>!</h4>
            </div>
        </div>

        <!-- Home Button -->
        <div class="home-btn">
            <a href="employee_dashboard.php" class="btn btn-primary">Home</a>
        </div>

        <!-- Logout Button -->
        <div class="logout-btn">
            <a href="?logout=true" class="btn btn-danger">Logout</a>
        </div>

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

        <!-- Select Product Form -->
        <form action="view_price_history.php" method="GET">
            <div class="form-group">
                <label for="product_id">Select Product</label>
                <select name="product_id" id="product_id" class="form-control" required>
                    <option value="">Select a Product</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['product_id']; ?>" <?php echo (isset($_GET['product_id']) && $_GET['product_id'] == $product['product_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($product['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">View Price History</button>
        </form>

        <!-- Price History Table -->
        <?php if (!empty($price_history)): ?>
            <h4 class="mt-4">Price History for <?php echo htmlspecialchars($price_history[0]['product_name']); ?></h4>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Change Date</th>
                        <th>Old Price</th>
                        <th>New Price</th>
                        <th>Change Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($price_history as $history): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($history['change_date']); ?></td>
                            <td><?php echo htmlspecialchars($history['old_price']); ?></td>
                            <td><?php echo htmlspecialchars($history['new_price']); ?></td>
                            <td><?php echo htmlspecialchars($history['change_reason']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (isset($_GET['product_id'])): ?>
            <p>No price history found for this product.</p>
        <?php endif; ?>

    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
