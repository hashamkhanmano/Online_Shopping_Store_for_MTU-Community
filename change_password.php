<?php
session_start();
require_once 'db.php'; // Database connection

// Initialize message variables
$message = '';
$redirect_url = 'index.php'; // Home URL or the page you want to redirect to

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and collect input data
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Check if new password matches confirmation
    if ($new_password !== $confirm_new_password) {
        $message = "New passwords do not match.";
    } else {
        // Hash the passwords for security
        $hashed_current_password = hash('sha256', $current_password);
        $hashed_new_password = hash('sha256', $new_password);

        // Connect to the database
        $conn = connectDB();
        if ($conn) {
            // Fetch current user from the session
            $customer_id = $_SESSION['customer_id'];

            // Check if the current password is correct
            $query = "SELECT * FROM Customer WHERE customer_id = :customer_id AND password = :current_password";
            $stmt = $conn->prepare($query);
            $stmt->execute(['customer_id' => $customer_id, 'current_password' => $hashed_current_password]);

            if ($stmt->rowCount() > 0) {
                // Update the password in the database
                $update_query = "UPDATE Customer SET password = :new_password WHERE customer_id = :customer_id";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->execute(['new_password' => $hashed_new_password, 'customer_id' => $customer_id]);

                // Password successfully updated
                $message = "Password updated successfully.";
                
                // Destroy the session to log the user out
                session_unset();  // Remove all session variables
                session_destroy(); // Destroy the session

                // Set redirect URL for login page or homepage
                $redirect_url = 'login.php'; // Assuming login.php is your login page
            } else {
                $message = "Current password is incorrect.";
            }
        } else {
            $message = "Database connection failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Change Confirmation</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Password Change Status</h3>
                </div>
                <div class="card-body">
                    <!-- Display the message -->
                    <div class="alert alert-info text-center">
                        <?php echo $message; ?>
                    </div>
                    
                    <?php if ($message == "Password updated successfully."): ?>
                        <!-- Success Message with Redirect Button -->
                        <div class="text-center">
                            <a href="<?php echo $redirect_url; ?>" class="btn btn-primary">Go to Login</a>
                        </div>
                    <?php else: ?>
                        <!-- Button to Try Again or go to Profile -->
                        <div class="text-center mt-3">
                            <a href="change_password.php" class="btn btn-secondary">Try Again</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS (with Popper.js) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
