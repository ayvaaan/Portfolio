<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

require_once 'config.php';

// Simple session-based authentication (use proper authentication in production)
session_start();

// Only allow authenticated requests (you can implement proper authentication)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get all messages or specific message
    
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        
        if ($action === 'get_all') {
            // Get all messages
            $sql = "SELECT id, name, email, message, created_at, status FROM contacts ORDER BY created_at DESC";
            $result = $conn->query($sql);
            
            if (!$result) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Query error: ' . $conn->error]);
                exit;
            }
            
            $messages = [];
            while ($row = $result->fetch_assoc()) {
                $messages[] = $row;
            }
            
            echo json_encode(['success' => true, 'messages' => $messages]);
            
        } elseif ($action === 'get_stats') {
            // Get message statistics
            $totalSql = "SELECT COUNT(*) as total FROM contacts";
            $unreadSql = "SELECT COUNT(*) as unread FROM contacts WHERE status = 'unread'";
            
            $totalResult = $conn->query($totalSql);
            $unreadResult = $conn->query($unreadSql);
            
            if (!$totalResult || !$unreadResult) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Query error']);
                exit;
            }
            
            $totalRow = $totalResult->fetch_assoc();
            $unreadRow = $unreadResult->fetch_assoc();
            
            echo json_encode([
                'success' => true,
                'total' => $totalRow['total'],
                'unread' => $unreadRow['unread']
            ]);
            
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown action']);
        }
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mark as read or delete message
    
    if (!isset($_POST['action']) || !isset($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing parameters']);
        exit;
    }
    
    $action = $_POST['action'];
    $id = intval($_POST['id']);
    
    if ($action === 'mark_read') {
        $sql = "UPDATE contacts SET status = 'read' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Message marked as read']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
        }
        $stmt->close();
        
    } elseif ($action === 'delete') {
        $sql = "DELETE FROM contacts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Message deleted']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
        }
        $stmt->close();
        
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }
}

$conn->close();
?>
