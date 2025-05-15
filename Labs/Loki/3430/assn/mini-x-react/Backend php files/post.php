<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';
require_once 'library.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$content = trim($data['content'] ?? '');

if (!$content) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Content is required.']);
    exit;
}

$pdo = connectDB();

$stmt = $pdo->prepare("INSERT INTO posts (user_id, content, created_at) VALUES (?, ?, NOW())");

if ($stmt->execute([$_SESSION['user_id'], $content])) {
    echo json_encode(['success' => true, 'message' => 'Post created']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
