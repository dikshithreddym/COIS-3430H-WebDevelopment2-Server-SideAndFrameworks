<?php
session_start(); // Start session 
require_once __DIR__ . '/includes/library.php'; // Include database connection
$pdo = connectDB();

// Initialize variables
$username = trim($_POST['username'] ?? "");
$email = trim($_POST['email'] ?? "");
$password = $_POST['password'] ?? "";
$confirmPassword = $_POST['confirmPassword'] ?? "";
$errors = [];

// When form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate username
    if (empty($username)) {
        $errors['username'] = "Username is required";
    }
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email address";
    }
    // Validate password length
    if (strlen($password) < 10) {
        $errors['password'] = "Password must be at least 10 characters long";
    }
    // Confirm password match
    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = "Passwords do not match";
    }

    if (empty($errors)) {
        // Hash password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $query = "INSERT INTO cois3430_lab_users (username, email, pwd) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$username, $email, $hashedPassword]);

        // Redirect to login page after successful registration
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="./styles/main.css">
</head>
<body>
    <header>
        <h1>Create Account</h1>
    </header>
    <main>
        <form method="post">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>
                <span class="error <?= empty($errors['username']) ? 'hidden' : '' ?>">
                    <?= $errors['username'] ?? '' ?>
                </span>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                <span class="error <?= empty($errors['email']) ? 'hidden' : '' ?>">
                    <?= $errors['email'] ?? '' ?>
                </span>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <span class="error <?= empty($errors['password']) ? 'hidden' : '' ?>">
                    <?= $errors['password'] ?? '' ?>
                </span>
            </div>
            <div>
                <label for="confirmPassword">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
                <span class="error <?= empty($errors['confirmPassword']) ? 'hidden' : '' ?>">
                    <?= $errors['confirmPassword'] ?? '' ?>
                </span>
            </div>
            <button type="submit">Create Account</button>
        </form>
        <a href="login.php">Already have an account? Login here.</a>
    </main>
    <footer>&copy; COIS 3430, Inc. 2024</footer>
</body>
</html>
