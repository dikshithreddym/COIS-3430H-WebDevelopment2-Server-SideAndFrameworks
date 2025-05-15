<?php
require_once 'config.php';
require_once 'library.php';
session_start();
header('Content-Type: application/json');

// Parse JSON input
$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');

$response = ['success' => false];

if (!$username || !$password) {
    $response['message'] = 'Username and password are required.';
    echo json_encode($response);
    exit;
}

$pdo = connectdb();

$stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $response['success'] = true;
    $response['username'] = $user['username'];
    $response['message'] = 'Login successful';
} else {
    $response['message'] = 'Invalid username or password.';
}

echo json_encode($response);
