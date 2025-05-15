<?php
header("Content-Type: application/json");
include '../includes/library.php'; 

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["username"]) || empty(trim($data["username"]))) {
    echo json_encode(["status" => "error", "message" => "Username required"]);
    exit();
}

$username = trim($data["username"]);
$pdo = connectdb();

$stmtUser = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmtUser->execute([$username]);
$user = $stmtUser->fetch();

if (!$user) {
    echo json_encode(["status" => "error", "message" => "User not found"]);
    exit();
}

$stmt = $pdo->prepare("SELECT content, created_at FROM posts WHERE user_id = ?");
$stmt->execute([$user['id']]);
$posts = $stmt->fetchAll();

echo json_encode([
    "status" => "success",
    "posts" => $posts ?: []
]);
