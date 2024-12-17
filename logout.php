<?php
// Start the session
session_start();

// Check if the user is logged in (session exists)
if (isset($_SESSION['customer_id'])) {
    // Get the customer_id from the session
    $customer_id = $_SESSION['customer_id'];

    // Include the db.php file for database connection
    require_once 'db.php';

    // Update the login_status to 0 (logged out) for the user
    $updateQuery = "UPDATE Customer SET login_status = 0 WHERE customer_id = :customer_id";
    executeQuery($updateQuery, ['customer_id' => $customer_id]);
}

// Destroy the session to log the user out
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit();
?>
