<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the db.php file for database connection
require_once 'db.php';

// Start the session
session_start();

// Initialize error variable to display on the login page
$error = '';

// Check if the user is already logged in
$is_logged_in = isset($_SESSION['customer_id']);

// Handle the form submission when the user submits the login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the username and password from the POST request
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check if the username exists in the database
    $query = "SELECT * FROM Customer WHERE username = :username";
    $stmt = executeQuery($query, ['username' => $username]);

    // Fetch the user data from the query result
    $user = $stmt->fetch();

    // If user exists and the password is correct
    if ($user && hash('sha256', $password) === $user['password']) {
        // Store the customer ID and username in the session
        $_SESSION['customer_id'] = $user['customer_id'];
        $_SESSION['username'] = $user['username'];

        // Update the last_login and login_status fields with the current time and login status
        $updateQuery = "UPDATE Customer SET last_login = NOW(), login_status = 1 WHERE customer_id = :customer_id";
        executeQuery($updateQuery, ['customer_id' => $user['customer_id']]);

        // Redirect the user to the main page after login
        header('Location: cust_main.php');
        exit();
    } else {
        // If the login fails, set the error message
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS for consistent styling -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
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

        .login-button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px;
            display: inline-block;
        }

        .login-button:hover {
            background-color: #218838;
        }

        .employee-login {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 50%;
            text-decoration: none;
            font-size: 18px;
            display: inline-block;
            position: absolute;
            right: 10px;
            top: 10px;
        }

        .employee-login:hover {
            background-color: #0056b3;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 30px;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        .centered-text {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2 class="centered-text">From Wishlist to Cart – Shop Now, Thank Us Later!</h2>

    <!-- Display login form if user is not logged in -->
    <?php if (!$is_logged_in): ?>
        <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="loginModalLabel">Login to Your Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Login Form -->
                        <form action="login.php" method="POST">
                            <!-- Show error message if login fails -->
                            <?php if ($error): ?>
                                <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                            </div>
                            <div class="form-group text-center mt-3">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100 text-center">
                            <small>Don't have an account? <a href="register.php">Register here</a></small><br>
                            <small><a href="forgot_password.php">Forgot your password?</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- If there are categories, display them -->
    <?php
    $query = "SELECT * FROM Category";
    $stmt = executeQuery($query);
    $categories = $stmt->fetchAll();
    
    if (count($categories) > 0): ?>
        <h3 class="centered-text">Browse Our Categories</h3>
        <div class="categories">
            <?php foreach ($categories as $category): ?>
                <div class="category">
                    <?php 
                    $image_path = "images/" . $category['category_id'] . ".jpg"; 
                    ?>
                    <?php if (file_exists($image_path)): ?>
                        <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                    <?php endif; ?>

                    <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                    <p><?php echo htmlspecialchars($category['description']); ?></p>
                    <a href="products.php?category_id=<?php echo $category['category_id']; ?>">View Products</a><br>

                    <?php if (!$is_logged_in): ?>
                        <a href="#" class="login-button" data-toggle="modal" data-target="#loginModal">Login to Add to Cart</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No categories found.</p>
    <?php endif; ?>

</div>

<!-- Footer at the bottom -->
<footer>
    <p>&copy; Welcome to Hasham Khan From MTU Online Store</p>
</footer>

<!-- Bootstrap JavaScript and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
