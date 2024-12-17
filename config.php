<?php
// Define constants for site URL and session timeout
define('SITE_URL', 'http://localhost/estore');
define('SESSION_TIMEOUT', 3600); // Session timeout in seconds

// Database connection details
define('DB_HOST', 'classdb.it.mtu.edu');      // Database host (e.g., 'localhost')
define('DB_NAME', 'hashamk');  // Database name
define('DB_USER', 'hashamk');  // Database username
define('DB_PASS', 'Huawei@786');  // Database password

// Create a PDO instance for database connection
try {
    // DSN (Data Source Name) for MySQL connection
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    
    // Set PDO error mode to exception for better error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // If there is an error connecting to the database, display an error message and stop execution
    die("Database connection failed: " . $e->getMessage());
}
?>
