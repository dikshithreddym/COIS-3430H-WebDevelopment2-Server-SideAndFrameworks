<?php
require_once __DIR__ . '/includes/library.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pdo = connectdb();

// Handle post submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $content = trim($_POST['content']);
    if (!empty($content) && strlen($content) <= 280) {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $content]);
        header('Location: feed.php');
        exit();
    } else {
        $error = 'Post must be between 1 and 280 characters.';
    }
}

// Handle post deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $post_id = $_POST['delete_post_id'];
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    header('Location: feed.php');
    exit();
}

// Handle post likes (toggle like/unlike)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_post_id'])) {
    $post_id = $_POST['like_post_id'];
    
    // Check if the user has already liked this post
    $stmtCheck = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
    $stmtCheck->execute([$_SESSION['user_id'], $post_id]);
    
    if ($stmtCheck->rowCount() > 0) {
         // User has already liked the post: remove the like
         $stmtUnlike = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
         $stmtUnlike->execute([$_SESSION['user_id'], $post_id]);
    } else {
         // User hasn't liked the post: add a like
         $stmtLike = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
         $stmtLike->execute([$_SESSION['user_id'], $post_id]);
    }
    
    header('Location: feed.php');
    exit();
}

// Fetch posts with like counts and include poster's user_id for messaging
$stmt = $pdo->query("SELECT posts.id, posts.user_id, posts.content, posts.created_at, users.username, 
                     (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count 
                     FROM posts JOIN users ON posts.user_id = users.id 
                     ORDER BY posts.created_at DESC");
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Feed</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <div class="post-box">
            <form method="POST" action="feed.php">
                <textarea name="content" placeholder="What's happening?" maxlength="280" required></textarea>
                <button type="submit" class="btn btn-primary mt-2">Post</button>
            </form>
        </div>
        <?php if (!empty($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>
        <h3 class="mt-4">Recent Posts</h3>
        <?php foreach ($posts as $post): ?>
            <div class="card mb-2 p-3">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($post['username']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($post['content']); ?></p>
                    <p class="text-muted small"><?php echo $post['created_at']; ?></p>
                    <p class="text-muted">Likes: <?php echo $post['like_count']; ?></p>
                    
                    <?php 
                    // Check if the current user has liked this post
                    $stmtLiked = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE user_id = ? AND post_id = ?");
                    $stmtLiked->execute([$_SESSION['user_id'], $post['id']]);
                    $userLiked = $stmtLiked->fetchColumn() > 0;
                    ?>
                    <form method="POST" action="feed.php" class="d-inline">
                        <input type="hidden" name="like_post_id" value="<?php echo $post['id']; ?>">
                        <button type="submit" class="btn-like">
                            <?php echo $userLiked ? 'Unlike' : 'Like'; ?>
                        </button>
                    </form>
                    
                    <?php if ($_SESSION['username'] === $post['username']): ?>
                        <form method="POST" action="feed.php" class="d-inline">
                            <input type="hidden" name="delete_post_id" value="<?php echo $post['id']; ?>">
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    <?php endif; ?>

                    <!-- Message Button: only show if the post is not by the current user -->
                    <?php if ($_SESSION['user_id'] !== $post['user_id']): ?>
                        <a href="direct_messages.php?recipient_id=<?php echo $post['user_id']; ?>" class="btn btn-link">Message</a>
                    <?php endif; ?>

                    <!-- Reply Button -->
                    <button class="btn btn-link" onclick="toggleReplyForm(<?php echo $post['id']; ?>)">Reply</button>

                    <!-- Hidden Reply Form -->
                    <div id="reply-form-<?php echo $post['id']; ?>" style="display: none;">
                        <form method="POST" action="reply.php">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <textarea name="reply_content" placeholder="Write your reply..." maxlength="280" required></textarea>
                            <button type="submit" class="btn btn-primary mt-2">Submit Reply</button>
                        </form>
                    </div>

                    <!-- Display Existing Replies -->
                    <?php 
                    $stmtReplies = $pdo->prepare("SELECT replies.*, users.username FROM replies JOIN users ON replies.user_id = users.id WHERE replies.post_id = ? ORDER BY replies.created_at ASC");
                    $stmtReplies->execute([$post['id']]);
                    $replies = $stmtReplies->fetchAll();
                    if ($replies):
                    ?>
                        <div class="replies mt-2">
                            <?php foreach ($replies as $reply): ?>
                                <div class="card p-2 mb-1">
                                    <strong><?php echo htmlspecialchars($reply['username']); ?></strong>: 
                                    <?php echo htmlspecialchars($reply['content']); ?>
                                    <br>
                                    <small class="text-muted"><?php echo $reply['created_at']; ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <p class="text-center mt-4"><a href="logout.php" class="btn btn-secondary">Logout</a></p>
    </div>
    
    <script>
    // Toggle the display of the reply form for a given post
    function toggleReplyForm(postId) {
        var form = document.getElementById("reply-form-" + postId);
        form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
    }
    </script>
</body>
</html>
