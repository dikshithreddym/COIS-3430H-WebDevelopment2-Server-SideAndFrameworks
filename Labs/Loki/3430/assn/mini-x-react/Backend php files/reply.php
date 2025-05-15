<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';
require_once 'library.php';

$pdo = connectDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $postId = $_GET['post_id'] ?? null;
    if (!$postId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Post ID is required']);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT r.content, r.created_at, u.username
        FROM replies r
        JOIN users u ON r.user_id = u.id
        WHERE r.post_id = ?
        ORDER BY r.created_at ASC
    ");
    $stmt->execute([$postId]);
    $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'replies' => $replies]);
    exit;
}

// Only run this block for POST
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$postId = $data['post_id'] ?? null;
$content = trim($data['content'] ?? '');

if (!$postId || !$content) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Post ID and content are required.']);
    exit;
}

// Validate post exists
$stmt = $pdo->prepare("SELECT id FROM posts WHERE id = ?");
$stmt->execute([$postId]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Post not found']);
    exit;
}

// Insert reply
$stmt = $pdo->prepare("
    INSERT INTO replies (post_id, user_id, content, created_at)
    VALUES (?, ?, ?, NOW())
");

if ($stmt->execute([$postId, $_SESSION['user_id'], $content])) {
    echo json_encode(['success' => true, 'message' => 'Reply submitted']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
