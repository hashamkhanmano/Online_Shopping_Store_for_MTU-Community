<?php
// Function to connect to the database using a configuration file
function connectDB()
{
    try {
        // Parse the database connection settings from the config file
        $config = parse_ini_file('db.ini'); // Adjust the path if necessary

        // Check if config is loaded correctly
        if (!$config) {
            throw new Exception("Unable to read the configuration file.");
        }

        // Create a PDO connection using values from the .ini file
        $dbh = new PDO($config['dsn'], $config['username'], $config['password']);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode to exceptions
        return $dbh;
    } catch (PDOException $e) {
        // Log and rethrow the exception if connection fails
        error_log("Database connection failed: " . $e->getMessage());
        throw new Exception("Database connection failed: " . $e->getMessage());
    }
}

// Function to get a database connection
function getConnection()
{
    return connectDB();
}

// Function to execute a query with parameters
function executeQuery($query, $params = [])
{
    // Get a connection to the database
    $pdo = getConnection();
    if (!$pdo) {
        throw new Exception("Database connection failed.");
    }

    // Prepare the SQL statement
    $stmt = $pdo->prepare($query);

    // Execute the query with parameters
    $stmt->execute($params);

    // Return the statement to fetch the results if necessary
    return $stmt;
}

// Function to execute an insert query (for placing orders)
function executeInsert($query, $params = [])
{
    // Get a connection to the database
    $pdo = getConnection();
    if (!$pdo) {
        throw new Exception("Database connection failed.");
    }

    // Begin the transaction
    $pdo->beginTransaction();

    // Prepare the SQL statement
    $stmt = $pdo->prepare($query);

    // Execute the query with parameters
    $stmt->execute($params);

    // Commit the transaction
    $pdo->commit();

    // Return the last inserted ID
    return $pdo->lastInsertId();
}
?>
