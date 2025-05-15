<?php
// Start the session and set JSON as the content type
session_start();
header('Content-Type: application/json');

// Include DB connection config and helper library
require_once 'config.php';
require_once 'library.php';

// Get the 'user' query parameter (i.e., the username)
$username = $_GET['user'] ?? '';

// If no username is provided, return a 400 Bad Request
if (!$username) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username is required']);
    exit;
}

// Connect to the database
$pdo = connectDB();

// Fetch user ID based on provided username
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

// If the user doesn't exist, return 404 Not Found
if (!$user) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Retrieve all posts made by this user along with the like count for each post
$stmt = $pdo->prepare("
    SELECT 
        p.id AS post_id,
        p.content,
        p.created_at,
        COUNT(l.id) AS likes
    FROM posts p
    LEFT JOIN likes l ON l.post_id = p.id
    WHERE p.user_id = ?
    GROUP BY p.id
    ORDER BY p.created_at DESC
");
$stmt->execute([$user['id']]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return posts as a success response
echo json_encode([
    'success' => true,
    'username' => $username,
    'posts' => $posts
]);
