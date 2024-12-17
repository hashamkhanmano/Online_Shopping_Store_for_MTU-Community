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

// Handle the form submission when the employee submits the login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the username and password from the POST request
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check if the employee exists in the database
    $query = "SELECT * FROM Employee WHERE username = :username";
    $stmt = executeQuery($query, ['username' => $username]);

    // Fetch the employee data from the query result
    $employee = $stmt->fetch();

    // If employee exists and the password matches (check hashed password)
    if ($employee) {
        // Check if password reset is required
        if ($employee['password_reset'] == 0) {
            // If password reset is required, show the password reset popup
            $_SESSION['employee_id'] = $employee['employee_id'];
            $_SESSION['employee_username'] = $employee['username'];
            $_SESSION['is_first_time_login'] = true;

            // Redirect to trigger reset password popup
            header('Location: employee_reset_password.php');
            exit();
        } else {
            // If password reset is not required, check password and login
            if (hash('sha256', $password) === $employee['password']) {
                // Store employee info in session
                $_SESSION['employee_id'] = $employee['employee_id'];
                $_SESSION['employee_username'] = $employee['username'];

                // Redirect the employee to their dashboard
                header('Location: employee_dashboard.php');
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        }
    } else {
        $error = "Employee not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .centered-text {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2 class="centered-text">Employee Login</h2>

    <!-- Display error message if login fails -->
    <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Employee login form -->
    <form action="employee_login.php" method="POST">
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

<!-- Footer -->
<footer>
    <p>&copy; Welcome to Hasham Khan Online Store</p>
</footer>

<!-- Bootstrap and JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
