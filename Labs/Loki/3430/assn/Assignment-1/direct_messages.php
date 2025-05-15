<?php
require_once __DIR__ . '/includes/library.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pdo = connectdb();

// Fetch all other users
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE id != ?");
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Determine recipient if one is selected
$recipient_id = isset($_GET['recipient_id']) ? intval($_GET['recipient_id']) : 0;

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_content'], $_GET['recipient_id'])) {
    $message_content = trim($_POST['message_content']);
    if (!empty($message_content)) {
        try {
            $stmtInsert = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
            $success = $stmtInsert->execute([$_SESSION['user_id'], $recipient_id, $message_content]);
            
            // Debugging (optional):
            // var_dump($stmtInsert->errorInfo());

            if (!$success) {
                echo "<p class='text-danger'>Error inserting message: " . htmlspecialchars($stmtInsert->errorInfo()[2]) . "</p>";
            } else {
                header("Location: direct_messages.php?recipient_id=" . $recipient_id);
                exit();
            }
        } catch (PDOException $e) {
            echo "<p class='text-danger'>PDO Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}

// Fetch conversation messages if recipient is set
$conversations = [];
if ($recipient_id > 0) {
    // Use unique placeholders so each parameter is bound correctly
    $stmtConv = $pdo->prepare("
        SELECT m.*, u.username AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE (m.sender_id = :current1 AND m.receiver_id = :recipient1)
           OR (m.sender_id = :recipient2 AND m.receiver_id = :current2)
        ORDER BY m.created_at ASC
    ");
    $stmtConv->execute([
        'current1'   => $_SESSION['user_id'],
        'recipient1' => $recipient_id,
        'recipient2' => $recipient_id,
        'current2'   => $_SESSION['user_id'],
    ]);
    $conversations = $stmtConv->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Direct Messages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Direct Messaging</h2>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>

        <!-- User Selection -->
        <div>
            <h4>Select a user to message:</h4>
            <ul>
                <?php foreach ($users as $user): ?>
                    <li>
                        <a href="direct_messages.php?recipient_id=<?php echo $user['id']; ?>">
                            <?php echo htmlspecialchars($user['username']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if ($recipient_id > 0): ?>
            <hr>
            <h4>Conversation</h4>
            <div style="height:300px; overflow-y:scroll; border:1px solid #ccc; padding:10px;">
                <?php if (count($conversations) > 0): ?>
                    <?php foreach ($conversations as $message): ?>
                        <div class="mb-2">
                            <strong><?php echo htmlspecialchars($message['sender_name']); ?>:</strong>
                            <?php echo htmlspecialchars($message['content']); ?>
                            <br>
                            <small class="text-muted"><?php echo $message['created_at']; ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No messages yet. Start the conversation!</p>
                <?php endif; ?>
            </div>

            <!-- Message Form -->
            <form method="POST" action="direct_messages.php?recipient_id=<?php echo $recipient_id; ?>" class="mt-3">
                <textarea name="message_content" class="form-control" placeholder="Type your message here..." required></textarea>
                <button type="submit" class="btn btn-primary mt-2">Send</button>
            </form>
        <?php endif; ?>

        <p class="mt-4"><a href="feed.php" class="btn btn-secondary">Back to Feed</a></p>
    </div>
</body>
</html>
