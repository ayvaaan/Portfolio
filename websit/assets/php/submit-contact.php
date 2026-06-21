<?php
header('Content-Type: application/json');
// CORS: allow only configured origin if set; otherwise allow all (use restrictive setting in production)
require_once __DIR__ . '/config.php';

if (ALLOWED_ORIGIN) {
    header('Access-Control-Allow-Origin: ' . ALLOWED_ORIGIN);
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Methods: POST');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data (support JSON payloads as well)
$input = $_POST;
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
        $input = array_merge($input, $json);
    }
}

$name = isset($input['name']) ? trim($input['name']) : '';
$email = isset($input['email']) ? trim($input['email']) : '';
$message = isset($input['message']) ? trim($input['message']) : '';

// Validate input
$errors = [];
if ($name === '') $errors[] = 'Name is required';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
if ($message === '' || mb_strlen($message) < 5) $errors[] = 'Message must be at least 5 characters long';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Sanitize for storage or store raw and escape on output. Here we store raw but strip dangerous tags.
$clean_name = strip_tags($name);
$clean_email = filter_var($email, FILTER_SANITIZE_EMAIL);
$clean_message = strip_tags($message, '<p><br><strong><em><ul><ol><li>'); // allow minimal formatting

try {
    $sql = "INSERT INTO contacts (name, email, message, status) VALUES (:name, :email, :message, 'unread')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $clean_name,
        ':email' => $clean_email,
        ':message' => $clean_message
    ]);

    $contactId = $pdo->lastInsertId();

    // Optional: trigger email notification if ALLOWED_NOTIFICATION_EMAIL is set in env
    $notifyTo = getenv('NOTIFY_EMAIL') ?: '';
    if ($notifyTo) {
        // Keep mail sending simple; consider using a proper mail library in production
        $subject = 'New Contact Message';
        $body = "Name: $clean_name\nEmail: $clean_email\nMessage:\n$clean_message";
        @mail($notifyTo, $subject, $body);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Your message has been sent successfully! I\'ll get back to you soon.',
        'contact_id' => $contactId
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    // Log $e->getMessage() to a file on the server if needed; don't reveal internals to the client
    error_log('DB error in submit-contact: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error saving message']);
}

?>
