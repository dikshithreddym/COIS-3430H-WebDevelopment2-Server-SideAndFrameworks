<?php
// Start session and set response to JSON format
session_start();
header('Content-Type: application/json');

// Include database config and helper functions
require_once 'config.php';
require_once 'library.php';

// Connect to the database using a helper function
$pdo = connectDB();

// Check if the user is authenticated via session
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id']; // Get current user's ID

// Handle GET and POST requests
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Expect a query string like: ?with=username
        $withUsername = $_GET['with'] ?? '';

        // Validate input
        if (!$withUsername) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Missing target username']);
            exit;
        }

        // Get the ID of the user to chat with
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$withUsername]);
        $targetUser = $stmt->fetch();

        if (!$targetUser) {
            http_response_code(404); // Not Found
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        $targetId = $targetUser['id'];

        // Retrieve messages between the logged-in user and the target user
        $stmt = $pdo->prepare("
            SELECT m.id, m.content AS message, m.created_at AS timestamp, u.username AS sender
            FROM messages m 
            JOIN users u ON m.sender_id = u.id
            WHERE (m.sender_id = ? AND m.receiver_id = ?)
               OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.created_at ASC
        ");
        $stmt->execute([$userId, $targetId, $targetId, $userId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return messages as JSON
        echo json_encode(['success' => true, 'messages' => $messages]);
        break;

    case 'POST':
        // Decode the incoming JSON body
        $data = json_decode(file_get_contents('php://input'), true);
        $toUsername = trim($data['to'] ?? '');
        $message = trim($data['message'] ?? '');

        // Validate message input
        if (!$toUsername || !$message) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Missing recipient or message']);
            exit;
        }

        // Find the recipient user ID
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$toUsername]);
        $receiver = $stmt->fetch();

        if (!$receiver) {
            http_response_code(404); // Not Found
            echo json_encode(['success' => false, 'message' => 'Recipient not found']);
            exit;
        }

        // Insert the new message into the database
        $stmt = $pdo->prepare("
            INSERT INTO messages (sender_id, receiver_id, content, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        if ($stmt->execute([$userId, $receiver['id'], $message])) {
            echo json_encode(['success' => true, 'message' => 'Message sent']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
        break;

    default:
        // Handle unsupported methods
        http_response_code(405); // Method Not Allowed
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        break;
}
