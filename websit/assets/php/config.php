<?php
// Load environment variables from a local .env file (optional) and export them into getenv().
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    $vars = parse_ini_file($envFile);
    if (is_array($vars)) {
        foreach ($vars as $k => $v) {
            if (getenv($k) === false) {
                putenv("$k=$v");
                $_ENV[$k] = $v;
            }
        }
    }
}

// Database configuration (use environment variables when available)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'portfolio_db');

// Connect using PDO for prepared statements and better error handling
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    // Don't leak credentials or internal info in production
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Admin credentials - DO NOT keep defaults in production. Prefer using admin_users table and password hashes.
define('ADMIN_USERNAME', getenv('ADMIN_USERNAME') ?: 'admin');
// Provide an ADMIN_PASSWORD_HASH environment variable (generated via password_hash()).
define('ADMIN_PASSWORD_HASH', getenv('ADMIN_PASSWORD_HASH') ?: password_hash('change_me', PASSWORD_DEFAULT));

// Optional: allowed origin for CORS (set ALLOWED_ORIGIN in .env to restrict)
// If empty, CORS is set to '*'. In production set this to your site URL.
define('ALLOWED_ORIGIN', getenv('ALLOWED_ORIGIN') ?: '');

?>
