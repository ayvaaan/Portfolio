<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once 'config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate input
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($message) || strlen($message) < 5) {
    $errors[] = 'Message must be at least 5 characters long';
}

// Return errors if validation fails
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Sanitize input
$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// Insert into database using prepared statement
$sql = "INSERT INTO contacts (name, email, message, status) VALUES (?, ?, ?, 'unread')";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

// Bind parameters
$stmt->bind_param('sss', $name, $email, $message);

// Execute query
if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Your message has been sent successfully! I\'ll get back to you soon.',
        'contact_id' => $stmt->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error saving message: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
