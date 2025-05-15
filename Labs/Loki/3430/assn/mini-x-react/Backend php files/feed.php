<?php
// Enable error reporting for debugging (useful during development)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start the session and set content type as JSON
session_start();
header('Content-Type: application/json');

// Include DB configuration and helper functions
require_once 'config.php';
require_once 'library.php';

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Establish database connection
$pdo = connectDB();

try {
    // Fetch posts along with:
    // - username of the poster
    // - number of likes (DISTINCT like IDs)
    // - number of replies (DISTINCT reply IDs)
    $stmt = $pdo->query("
        SELECT 
            p.id AS post_id,
            p.content,
            p.created_at,
            u.username,
            COUNT(DISTINCT l.id) AS likes,
            COUNT(DISTINCT r.id) AS replies
        FROM posts p
        JOIN users u ON p.user_id = u.id
        LEFT JOIN likes l ON l.post_id = p.id
        LEFT JOIN replies r ON r.post_id = p.id
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ");

    // Fetch all posts as associative array
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return success response with post data
    echo json_encode(['success' => true, 'posts' => $posts]);

} catch (PDOException $e) {
    // Handle any DB exceptions with a 500 error
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
