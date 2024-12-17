<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the db.php file for database connection
require_once 'db.php';

// Start the session
session_start();

// Check if employee is logged in for the first time
if (!isset($_SESSION['employee_id']) || !isset($_SESSION['is_first_time_login']) || $_SESSION['is_first_time_login'] !== true) {
    header('Location: employee_login.php');
    exit();
}

// Initialize error message
$error = '';

// Handle form submission for password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the new password and confirm password
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($new_password === $confirm_password) {
        // Hash the new password using SHA-256 (you could also use bcrypt for stronger security)
        $hashed_password = hash('sha256', $new_password);

        // Update the employee's password in the database and set password_reset to FALSE
        $employee_id = $_SESSION['employee_id'];
        $query = "UPDATE Employee SET password = :password, password_reset = 1 WHERE employee_id = :employee_id";
        executeQuery($query, ['password' => $hashed_password, 'employee_id' => $employee_id]);

        // Remove first-time login session flag
        unset($_SESSION['is_first_time_login']);

        // Redirect the employee to their dashboard after resetting the password
        header('Location: employee_dashboard.php');
        exit();
    } else {
        // Display error if passwords don't match
        $error = "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Employee Password</title>
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
    <h2 class="centered-text">Reset Your Password</h2>

    <!-- Display error message if passwords do not match -->
    <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Password reset form -->
    <form action="employee_reset_password.php" method="POST">
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
        </div>
        <div class="form-group text-center mt-3">
            <button type="submit" class="btn btn-primary">Reset Password</button>
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
