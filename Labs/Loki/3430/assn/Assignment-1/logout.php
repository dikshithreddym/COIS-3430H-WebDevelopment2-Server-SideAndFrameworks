<?php
require_once __DIR__ . '/includes/library.php';
session_start();

// Logout and destroy session
session_unset();
session_destroy();

header('Location: login.php');
exit();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>You have been logged out.</h2>
    <p><a href="login.php">Login again</a></p>
</body>
</html>
