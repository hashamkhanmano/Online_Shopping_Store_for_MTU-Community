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

// Fetch categories from the database to populate the category dropdown
$query = "SELECT category_id, name FROM Category";
$stmt = executeQuery($query);
$categories = $stmt->fetchAll();

// Handle form submission and insert the new product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $product_name = $_POST['name'];
    $product_description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $advising_threshold = $_POST['advising_threshold'];
    $category_id = $_POST['category_id'];
    $image_name = $_POST['image'];  // Only the image name (without directory)
    $created_at = date("Y-m-d H:i:s");
    $change_date = date("Y-m-d H:i:s");

    // Validate data
    if (empty($product_name) || empty($price) || empty($stock_quantity) || empty($category_id) || empty($image_name)) {
        $_SESSION['error'] = "Please fill all required fields.";
        header('Location: add_product.php'); // Redirect back to the form
        exit();
    }

    // Assuming all images are stored in the same directory
    $image_url = 'images/' . $image_name;  // Add 'images/' directory prefix

    // Prepare the SQL query to insert the product into the database
    $query = "INSERT INTO Product (name, description, price, stock_quantity, advising_threshold, category_id, created_at, image, change_date) 
              VALUES (:name, :description, :price, :stock_quantity, :advising_threshold, :category_id, :created_at, :image, :change_date)";
    
    $params = [
        'name' => $product_name,
        'description' => $product_description,
        'price' => $price,
        'stock_quantity' => $stock_quantity,
        'advising_threshold' => $advising_threshold,
        'category_id' => $category_id,
        'created_at' => $created_at,
        'image' => $image_url,
        'change_date' => $change_date
    ];

    try {
        // Execute the query
        $stmt = executeQuery($query, $params);
        $_SESSION['success'] = "Product added successfully!";
        header('Location: product_dashboard.php'); // Redirect to product dashboard
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Failed to add product. Please try again.";
        header('Location: add_product.php'); // Redirect back to the form if error
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; // Assuming you have a header file ?>

    <div class="container mt-4">
        <h2>Add New Product</h2>

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

        <!-- Product Form -->
        <form action="add_product.php" method="POST">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="description">Product Description</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="price">Product Price</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="stock_quantity">Product Stock Quantity</label>
                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required>
            </div>

            <div class="form-group">
                <label for="advising_threshold">Advising Threshold</label>
                <input type="number" class="form-control" id="advising_threshold" name="advising_threshold" required>
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <option value="">Select a Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Image Name Input (only the image file name) -->
            <div class="form-group">
                <label for="image">Product Image Name (e.g. product.jpg)</label>
                <input type="text" class="form-control" id="image" name="image" required>
                <small class="form-text text-muted">The image should be placed in the "images" directory.</small>
            </div>

            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
