<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'jpsme_event');
define('DB_USER', 'root'); // Change this for production
define('DB_PASS', '');     // Change this for production
define('DB_CHARSET', 'utf8mb4');

// Security settings
define('UPLOAD_MAX_SIZE', 5242880); // 5MB
define('ALLOWED_FILE_TYPES', ['pdf', 'jpg', 'jpeg', 'png']);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_LOGIN_ATTEMPTS', 5);
define('RATE_LIMIT_REQUESTS', 10);
define('RATE_LIMIT_WINDOW', 3600); // 1 hour

// Create mysqli connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}

// Set charset
$conn->set_charset(DB_CHARSET);
?>
