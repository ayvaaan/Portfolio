<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

// Start session and check authentication
session_start();

// CORS
if (ALLOWED_ORIGIN) {
    header('Access-Control-Allow-Origin: ' . ALLOWED_ORIGIN);
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Methods: GET, POST');

if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_GET['action'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing action']);
            exit;
        }

        $action = $_GET['action'];

        if ($action === 'get_all') {
            $stmt = $pdo->query("SELECT id, name, email, message, created_at, status FROM contacts ORDER BY created_at DESC");
            $messages = $stmt->fetchAll();
            echo json_encode(['success' => true, 'messages' => $messages]);
            exit;
        } elseif ($action === 'get_stats') {
            $total = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
            $unread = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'unread'")->fetchColumn();
            echo json_encode(['success' => true, 'total' => (int)$total, 'unread' => (int)$unread]);
            exit;
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown action']);
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Accept form or JSON
        $input = $_POST;
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $json = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                $input = array_merge($input, $json);
            }
        }

        if (!isset($input['action']) || !isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit;
        }

        $action = $input['action'];
        $id = (int)$input['id'];

        if ($action === 'mark_read') {
            $stmt = $pdo->prepare("UPDATE contacts SET status = 'read' WHERE id = :id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['success' => true, 'message' => 'Message marked as read']);
            exit;
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = :id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['success' => true, 'message' => 'Message deleted']);
            exit;
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown action']);
            exit;
        }
    }
} catch (PDOException $e) {
    error_log('get-messages error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}

?>
