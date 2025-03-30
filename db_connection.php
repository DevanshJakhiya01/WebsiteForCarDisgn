<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'car_customization');

// Attempt to connect to MySQL database
try {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4 for proper encoding support
    $conn->set_charset("utf8mb4");
    
    // Error reporting (only for development - disable in production)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

/**
 * Sanitize input data
 * @param string $data The input to sanitize
 * @return string The sanitized data
 */
function sanitize_input($data) {
    global $conn;
    return htmlspecialchars(stripslashes(trim($conn->real_escape_string($data))));
}

/**
 * Close database connection
 */
function close_db_connection() {
    global $conn;
    if (isset($conn)) {
        $conn->close();
    }
}

// Register shutdown function to ensure connection is closed
register_shutdown_function('close_db_connection');

// Set timezone if needed
date_default_timezone_set('UTC'); // Change to your preferred timezone
?>