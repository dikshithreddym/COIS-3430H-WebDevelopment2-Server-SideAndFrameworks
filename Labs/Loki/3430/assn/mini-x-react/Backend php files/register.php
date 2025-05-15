<?php
session_start();

//  Proper headers for CORS with credentials
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once 'config.php';
require_once 'library.php';

//  Decode JSON input
$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

//  Validate input
if (!$username || !$email || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

$pdo = connectDB();

// Check if username or email already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Username or email already exists.']);
    exit;
}

// Hash password and insert user
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");

if ($stmt->execute([$username, $email, $hashedPassword])) {
    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['username'] = $username;

    echo json_encode([
        'success' => true,
        'message' => 'Registration successful',
        'username' => $username,
        'user_id' => $_SESSION['user_id']
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error creating user. Please try again.']);
}
