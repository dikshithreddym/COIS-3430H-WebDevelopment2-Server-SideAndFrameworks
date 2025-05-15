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
$postId = $data['post_id'] ?? null;

if (!$postId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Post ID required']);
    exit;
}

$pdo = connectDB();

// Check if user already liked it
$stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
$stmt->execute([$_SESSION['user_id'], $postId]);
$like = $stmt->fetch();

if ($like) {
    // Unlike
    $pdo->prepare("DELETE FROM likes WHERE id = ?")->execute([$like['id']]);
    $userLiked = false;
} else {
    // Like
    $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)")->execute([$_SESSION['user_id'], $postId]);
    $userLiked = true;
}

// Return new like count
$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM likes WHERE post_id = ?");
$stmt->execute([$postId]);
$totalLikes = $stmt->fetchColumn();

echo json_encode([
    'success' => true,
    'likes' => (int)$totalLikes,
    'user_liked' => $userLiked
]);
