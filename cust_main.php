<?php
// Include the database connection and functions file
require_once 'db.php';  // Make sure db.php is in the same directory

// Start the session
session_start();

// Fetch categories from the database
$query = "SELECT * FROM Category";  // Query to get categories from the Category table
$stmt = executeQuery($query);  // Execute the query
$categories = $stmt->fetchAll();  // Fetch all the categories

// Check if the user is logged in (using session variable)
$is_logged_in = isset($_SESSION['customer_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Categories</title>

    <!-- Bootstrap CSS for consistent styling -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Make the body fill the screen height */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        .main-container {
            display: flex;
            flex-direction: column;
            min-height: 92.8%;
        }

        .container {
            flex: 1; /* This will take up available space */
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 30px;
            /* Ensure the footer sticks to the bottom if content is not enough */
            position: relative;
            bottom: 0;
            width: 100%;
        }

        .categories {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
        }

        .category {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 250px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .category:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .category img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .category h3 {
            margin: 10px 0;
            font-size: 18px;
            color: #007bff;
        }

        .category a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .category a:hover {
            background-color: #0056b3;
        }

    </style>

</head>
<body>

<?php include 'header.php';  // Include the header with the username, cart, and logout ?>

<div class="main-container">
    <div class="container">
        <h2>"Shop smart, shop with Hasham Khan – where every product tells a story!"</h2>

        <?php
        // If there are categories, display them
        if (count($categories) > 0) {
            echo "<div class='categories'>";
            // Loop through the categories and display each as a card with a link
            foreach ($categories as $category) {
                // Construct the image path for each category
                $image_path = "images/" . $category['category_id'] . ".jpg";  // Assuming the images are named by category_id (1.jpg, 2.jpg, etc.)

                echo "<div class='category'>
                        <img src='" . htmlspecialchars($image_path) . "' alt='" . htmlspecialchars($category['name']) . "'>
                        <h3>{$category['name']}</h3>
                        <p>{$category['description']}</p>
                        <a href='products.php?category_id={$category['category_id']}'>View Products</a>
                      </div>";
            }
            echo "</div>";
        } else {
            echo "<p>No categories found.</p>";
        }
        ?>
    </div>

    <!-- Footer at the bottom -->
    <footer>
        <p>&copy; Fall 2024 Hasham Khan Online Store</p>
    </footer>
</div>

<!-- Bootstrap JavaScript and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
