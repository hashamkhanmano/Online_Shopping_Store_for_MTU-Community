<?php
// Fetch product details from the database
$product_id = $_GET['product_id'] ?? 0;  // Get the product ID from the URL
$query = "SELECT * FROM Product WHERE product_id = :product_id"; // Query to fetch the product details
$stmt = executeQuery($query, ['product_id' => $product_id]);  // Execute the query
$product = $stmt->fetch();

// Check if the product exists
if (!$product) {
    echo "<p>Product not found!</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - Product Details</title>

    <!-- Add CSS for styling -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            background-color: #007bff;
            padding: 20px;
            text-align: center;
            color: white;
            font-size: 24px;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .product-details {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .product-image {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
        }

        .product-image img {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .product-info {
            flex: 1;
            max-width: 600px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .product-info h1 {
            font-size: 28px;
            color: #007bff;
            margin-bottom: 15px;
        }

        .product-info p {
            font-size: 16px;
            margin-bottom: 15px;
        }

        .product-info .price {
            font-size: 24px;
            font-weight: bold;
            color: #ff5722;
            margin-bottom: 20px;
        }

        .product-info .quantity {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .product-info .quantity label {
            margin-right: 10px;
        }

        .product-info input[type="number"] {
            padding: 5px;
            font-size: 16px;
            width: 50px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .add-to-cart {
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            text-align: center;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .add-to-cart:hover {
            background-color: #0056b3;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
            margin-top: 30px;
        }

    </style>
</head>
<body>

<header>
    <h1><?php echo $product['name']; ?> - Product Details</h1>
</header>

<div class="container">
    <div class="product-details">
        <!-- Product Image Section -->
        <div class="product-image">
            <!-- Use the full URL for the image -->
            src="https://classdb.it.mtu.edu/~hashamk/images/<?php echo $product['image']; ?>"
        </div>

        <!-- Product Info Section -->
        <div class="product-info">
            <h1><?php echo $product['name']; ?></h1>
            <p><?php echo $product['description']; ?></p>

            <!-- Price Display -->
            <p class="price">$<?php echo number_format($product['price'], 2); ?></p>

            <!-- Quantity Selection -->
            <div class="quantity">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" value="1" min="1" required>
            </div>

            <!-- Add to Cart Button -->
            <form action="add_to_cart.php" method="GET">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
                <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                <input type="hidden" name="product_image" value="<?php echo $product['image']; ?>">

                <button type="submit" class="add-to-cart">Add to Cart</button>
            </form>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2024 Your Online Store</p>
</footer>

</body>
</html>
