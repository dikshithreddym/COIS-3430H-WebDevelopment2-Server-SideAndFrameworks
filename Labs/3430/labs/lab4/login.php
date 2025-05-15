<?php
session_start(); // Start session at the top
require_once 'includes/library.php'; // Include database connection

// Initialize variables
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$errors = [];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    }
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    if (empty($errors)) {
        try {
            // Connect to the database
            $pdo = connectDB();
            
            // Fetch user details
            $stmt = $pdo->prepare("SELECT userID, username, pwd FROM cois3430_lab_users WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['pwd'])) {
                // Successful login, set session variables
                $_SESSION['userID'] = $user['userID'];
                $_SESSION['username'] = $user['username'];
                header("Location: address.php"); // Redirect
                exit;
            } else {
                $errors['login'] = 'Invalid username or password';
            }
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./styles/main.css">
</head>
<body>
    <header>
        <h1>My Address Book</h1>
    </header>
    <main>
        <nav>
            <h2>Menu</h2>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="contact.php">Add Contact</a></li>
                <li><a href="address.php">View Address Book</a></li>
            </ul>
        </nav>

        <div id="center-container">
            <h2>Login</h2>
            <form id="login" method="post">
                <div class="form-item col">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" size="25" value="<?= htmlspecialchars($username) ?>" />
                    <span class="error <?= empty($errors['username']) ? 'hidden' : '' ?>">
                        <?= $errors['username'] ?? '' ?>
                    </span>
                </div>
                <div class="form-item col">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" size="25" />
                    <span class="error <?= empty($errors['password']) ? 'hidden' : '' ?>">
                        <?= $errors['password'] ?? '' ?>
                    </span>
                </div>
                <div class="form-item col">
                    <span class="error <?= empty($errors['login']) ? 'hidden' : '' ?>">
                        <?= $errors['login'] ?? '' ?>
                    </span>
                </div>
                <div class="form-item row">
                    <label for="remember">Remember:</label>
                    <input type="checkbox" name="remember" value="remember" />
                </div>
                <button id="submit" name="submit" class="centered">Login</button>
            </form>
            <a href="create-account.php">Create a New Account</a>
        </div>
    </main>
    <footer>&copy; COIS 3430, Inc. 2024</footer>
</body>
</html>
