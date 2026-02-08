<?php // Provides a shared PDO connection
// Create or resume a session to track authenticated users
if (session_status() === PHP_SESSION_NONE) { session_start(); } // Ensure session is active

// Define MySQL host (XAMPP default localhost)
$DB_HOST = 'localhost'; // Database host
// Define MySQL database name
$DB_NAME = 'charm_app_db'; // Database name
// Define MySQL username
$DB_USER = 'root'; // Database user
// Define MySQL password (empty by default on XAMPP)
$DB_PASS = ''; // Database password

try { // Attempt to create a PDO connection
    // Build DSN string with charset
    $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4"; // DSN with charset
    // Configure PDO options for safety
    $options = [ // PDO options array
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw exceptions on error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Return associative arrays
        PDO::ATTR_EMULATE_PREPARES => false, // Use real prepared statements
    ];
    // Instantiate PDO
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options); // Create PDO instance
} catch (PDOException $e) { // Handle connection errors
    // Stop execution with an error message
    exit('Database connection failed: ' . $e->getMessage()); // Fail fast
}
