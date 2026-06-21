<?php
// Database Configuration
// Update these values with your actual database credentials

define('DB_HOST', 'localhost'); // Your database host
define('DB_USER', 'root'); // Your database username
define('DB_PASS', 'ivangwapo123'); // Your database password
define('DB_NAME', 'portfolio_db'); // Your database name

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Define admin credentials (change these to your actual credentials)
define('ADMIN_USERNAME', 'ivan');
define('ADMIN_PASSWORD', 'ivangwapo123'); // Change this to a strong password

?>
