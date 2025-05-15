<?php
require_once __DIR__ . '/includes/library.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pdo = connectdb();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['reply_content'])) {
    $post_id = $_POST['post_id'];
    $reply_content = trim($_POST['reply_content']);

    if (!empty($reply_content) && strlen($reply_content) <= 280) {
        $stmt = $pdo->prepare("INSERT INTO replies (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $_SESSION['user_id'], $reply_content]);
        header("Location: feed.php");
        exit();
    } else {
        echo "Reply must be between 1 and 280 characters.";
    }
}
?>
