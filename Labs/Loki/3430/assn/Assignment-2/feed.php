<?php
// Load library file (for DB connection)
require_once __DIR__ . '/includes/library.php';
session_start();

// Start the session to access session variables
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Connect to the database using helper function
$pdo = connectdb();

// =========================
// Handle new post creation
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $content = trim($_POST['content']);
    if (!empty($content) && strlen($content) <= 280) {
        // Insert new post into database
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $content]);
        header('Location: feed.php');  // Refresh the page to show the post
        exit();
    } else {
        $error = 'Post must be between 1 and 280 characters.';
    }
}

// =========================
// Handle post deletion
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $post_id = $_POST['delete_post_id'];
    // Only allow deletion by the original poster
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    header('Location: feed.php');
    exit();
}

// =========================
// Handle like/unlike toggle
// =========================
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

// =========================
// Fetch all posts to display
// =========================
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
      <!-- Load Bootstrap for styling -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- Weather section with city search -->
<div class="card p-3 mb-3" style="background: #192734; color: white;" id="weather-box">
    <form onsubmit="searchWeather(event)" class="d-flex justify-content-center mb-2">
        <input type="text" id="citySearch" placeholder="Search city..." class="form-control w-50 me-2">
        <button class="btn btn-light">Search</button>
    </form>
    <div id="weather" class="text-center">
        <p>Fetching weather...</p>
    </div>
</div>
    <div class="container mt-4">
        <!-- Welcome message -->
        <h2 class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
         <!-- Post form -->
        <div class="post-box">
            <form method="POST" action="feed.php">
                <textarea name="content" placeholder="What's happening?" maxlength="280" required></textarea>
                <button type="submit" class="btn btn-primary mt-2">Post</button>
            </form>
        </div>

     <!-- Username search for posts -->
    <div class="post-box">
        <h3>Retrieve User Posts</h3>
        <input type="text" id="usernameInput" placeholder="Enter username">
        <button onclick="getUserPosts()" class="btn btn-primary">Get Posts</button>
    </div>
<div id="userPosts"></div>
        <?php if (!empty($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>
        <!-- Display all recent posts -->
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
    // Function to toggle the reply form for a specific post
    function toggleReplyForm(postId) {
        // Get the reply form element for the given post ID
        var form = document.getElementById("reply-form-" + postId);
        // Toggle the form's display between none and block
        form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
    }

    // When the document is fully loaded
    document.addEventListener("DOMContentLoaded", function () {
        // Check if geolocation is supported by the browser
        if (navigator.geolocation) {
            // Try to get the user's current position
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    // If successful, fetch weather using coordinates
                    fetchWeather(position.coords.latitude, position.coords.longitude);
                },
                () => fetchWeatherFallback() // If denied or error, use fallback
            );
        } else {
            // If geolocation is not supported, use fallback
            fetchWeatherFallback();
        }
    });

    // Fetch weather data using coordinates (latitude & longitude)
    function fetchWeather(lat, lon) {
        fetch(`weather.php?lat=${lat}&lon=${lon}`)
            .then(res => res.json()) // Convert response to JSON
            .then(data => displayWeather(data)) // Display weather on success
            .catch(() => fetchWeatherFallback()); // Fallback on failure
    }

    // Fetch default weather when geolocation is unavailable or denied
    function fetchWeatherFallback() {
        fetch(`weather.php`)
            .then(res => res.json())
            .then(data => displayWeather(data));
    }

    // Function to search weather by city input from user
    function searchWeather(event) {
        event.preventDefault(); // Prevent form from submitting normally
        const city = document.getElementById("citySearch").value.trim(); // Get input value
        if (city) {
            fetch(`weather.php?city=${encodeURIComponent(city)}`) // Send request with city param
                .then(res => res.json())
                .then(data => displayWeather(data));
        }
    }

    // Display weather data in the weather UI box
    function displayWeather(data) {
        const weatherBox = document.getElementById("weather");
        if (data.status === "success") {
            // Render weather info
            weatherBox.innerHTML = `
                <h5>${data.city}, ${data.country}</h5>
                <p><img src="${data.icon}" style="height:24px;"> ${data.temperature}°C - ${data.description}</p>
            `;
        } else {
            // Show message if weather info is unavailable
            weatherBox.innerHTML = `<p class="text-warning">${data.message || 'Weather unavailable.'}</p>`;
        }
    }

    // Function to get posts of a specific user by username
    function getUserPosts() {
        let username = document.getElementById("usernameInput").value.trim(); // Get the entered username

        fetch("api/getUserPosts.php", {
            method: "POST", // Use POST request
            headers: { "Content-Type": "application/json" }, // Set JSON content type
            body: JSON.stringify({ username: username }) // Send username as JSON body
        })
        .then(response => response.json()) // Convert response to JSON
        .then(data => {
            let postContainer = document.getElementById("userPosts"); // Target div for displaying posts
            postContainer.innerHTML = ""; // Clear previous content

            if (data.status === "success") {
                // Loop through and display each post
                data.posts.forEach(post => {
                    postContainer.innerHTML += `
                        <div class="card p-3 my-2">
                            <p>${post.content}</p>
                            <small class="text-muted">${post.created_at}</small>
                        </div>`;
                });
            } else {
                // Show error message if fetching failed
                postContainer.innerHTML = `<p class="text-danger">${data.message}</p>`;
            }
        });
    }
</script>

</body>
</html>
