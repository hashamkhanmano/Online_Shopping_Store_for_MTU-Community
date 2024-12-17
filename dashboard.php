<?php
// dashboard.php
session_start();

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];  // Get the logged-in user's username
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h3>Welcome, <?php echo htmlspecialchars($username); ?>!</h3>
    <p>You are logged in.</p>
    <a href="logout.php">Logout</a>
</body>
</html>
