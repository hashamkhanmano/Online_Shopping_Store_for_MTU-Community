<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';  // Include the db.php file

// Initialize message variable
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize input data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : null;
    $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $shipping_address = isset($_POST['shipping_address']) ? $_POST['shipping_address'] : null;

    // Hash the password before storing it
    $hashed_password = hash('sha256', $password); // Matching your db schema for password hashing

    // Get the database connection using the connectDB() function
    $conn = connectDB();

    if ($conn) {
        // Check if username already exists
        $query = "SELECT * FROM Customer WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->execute(['username' => $username]);

        // If the username exists, return an error
        if ($stmt->rowCount() > 0) {
            $message = "Username already exists!";
        } else {
            // Insert new user into the database
            $query = "INSERT INTO Customer (username, password, first_name, last_name, email, shipping_address)
                      VALUES (:username, :password, :first_name, :last_name, :email, :shipping_address)";

            $stmt = $conn->prepare($query);
            $params = [
                'username' => $username,
                'password' => $hashed_password,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'shipping_address' => $shipping_address
            ];

            if ($stmt->execute($params)) {
                $message = "Registration successful! You can now <a href='#' data-toggle='modal' data-target='#loginModal'>Login</a>";
            } else {
                $message = "Error: Unable to register user.";
            }
        }

        // Close the database connection
        $conn = null;
    } else {
        $message = "Database connection failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Register</h3>
                </div>
                <div class="card-body">
                    <!-- Display success or error message -->
                    <?php if ($message): ?>
                        <div class="alert alert-info text-center"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <!-- Registration Form -->
                    <form method="POST" action="register.php">
                        <div class="form-group">
                            <label for="username">Username (required):</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password (required):</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="first_name">First Name (optional):</label>
                            <input type="text" class="form-control" id="first_name" name="first_name">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name (optional):</label>
                            <input type="text" class="form-control" id="last_name" name="last_name">
                        </div>
                        <div class="form-group">
                            <label for="email">Email (optional):</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="shipping_address">Shipping Address (optional):</label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address"></textarea>
                        </div>

                        <!-- Register Button -->
                        <button type="submit" class="btn btn-primary btn-block">Register</button>

                        <!-- Cancel Button with JavaScript to redirect to login page -->
                        <button type="button" class="btn btn-secondary btn-block" onclick="window.location.href='login.php';">Cancel</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <small>Already have an account? <a href="#" data-toggle="modal" data-target="#loginModal">Login here</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Login Modal -->
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
                    <div class="form-group">
                        <label for="login_username">Username</label>
                        <input type="text" class="form-control" id="login_username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="login_password">Password</label>
                        <input type="password" class="form-control" id="login_password" name="password" required>
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

<!-- Bootstrap JavaScript and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
