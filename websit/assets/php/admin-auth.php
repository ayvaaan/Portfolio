<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

// CORS
if (ALLOWED_ORIGIN) {
    header('Access-Control-Allow-Origin: ' . ALLOWED_ORIGIN);
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Methods: POST');

// Accept only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Read input (form or JSON)
$input = $_POST;
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
        $input = array_merge($input, $json);
    }
}

$username = isset($input['username']) ? trim($input['username']) : '';
$password = isset($input['password']) ? $input['password'] : '';

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username and password required']);
    exit;
}

session_start();

try {
    // Prefer admin_users table if present
    $stmt = $pdo->prepare('SELECT username, password_hash FROM admin_users WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $username]);
    $row = $stmt->fetch();

    $valid = false;
    if ($row) {
        if (password_verify($password, $row['password_hash'])) {
            $valid = true;
        }
    } else {
        // Fallback to environment hash if table/user not found
        if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
            $valid = true;
        }
    }

    if ($valid) {
        session_regenerate_id(true);
        $_SESSION['admin_authenticated'] = true;
        $_SESSION['admin_username'] = $username;
        echo json_encode(['success' => true, 'message' => 'Authenticated']);
        exit;
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        exit;
    }
} catch (PDOException $e) {
    error_log('admin-auth error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}

?>
