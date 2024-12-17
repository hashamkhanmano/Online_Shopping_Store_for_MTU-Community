<?php
// Start the session to check login status
session_start();

// Include the database connection file
require_once 'db.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['customer_id']);

// Fetch the category ID from the URL
$category_id = $_GET['category_id'] ?? 0;

// Query to fetch products based on the category
$query = "SELECT * FROM Product WHERE category_id = :category_id";
$stmt = executeQuery($query, ['category_id' => $category_id]);
$products = $stmt->fetchAll();

// If no products are found in the category
if (!$products) {
    echo "<h2>No products found in this category!</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS for styling the product cards -->
    <style>
        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        /* Product Card Styling */
        .product-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .product-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .product-info {
            padding: 15px;
        }

        .product-info h3 {
            font-size: 1.2rem;
            color: #333;
        }

        .product-info p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
        }

        .price {
            font-size: 1.1rem;
            font-weight: bold;
            color: #007bff;
        }

        .add-to-cart-btn {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .add-to-cart-btn:hover {
            background-color: #218838;
        }

        /* Modal and overlay */
        .login-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }

        .login-prompt {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            max-width: 400px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Our Products</h2>
    
    <!-- Grid for displaying products -->
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <!-- Product Image -->
                <div class="product-image">
                    <img src="https://classdb.it.mtu.edu/~hashamk/images/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>

                <!-- Product Info -->
                <div class="product-info">
                    <h3><?php echo $product['name']; ?></h3>
                    <p><?php echo substr($product['description'], 0, 100) . '...'; ?></p>
                    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>

                    <?php if ($is_logged_in): ?>
                        <!-- Add to Cart Form (Only for logged-in users) -->
                        <form action="add_to_cart.php" method="GET">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
                            <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                            <input type="hidden" name="product_image" value="<?php echo $product['image']; ?>">
                            <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                        </form>
                    <?php else: ?>
                        <!-- Login Prompt for Non-Logged In Users -->
                        <button class="add-to-cart-btn" data-toggle="modal" data-target="#loginModal">Login to Add to Cart</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Login Modal (Ensure this is similar to the one in your login page) -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login to Continue</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
            </div>
            <div class="modal-footer">
                <small>Don't have an account? <a href="register.php">Register here</a></small>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JavaScript and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
