<?php
session_start(); // Start session

// Redirect if not logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

require_once 'library.php'; // Include database connection
$pdo = connectDB();

// Get user ID from session
$userID = $_SESSION['userID'];

// Get data from POST
$name = trim($_POST['name'] ?? "");
$email = trim($_POST['email'] ?? "");
$phoneType = $_POST['phone-type'] ?? "";
$area = trim($_POST['area-code'] ?? "");
$phone = trim($_POST['phone'] ?? "");
$ext = trim($_POST['ext'] ?? "");
$contactType = $_POST['contact-type'] ?? [];
$errors = [];

// When form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate name
    if (empty($name)) {
        $errors['name'] = "Name is required";
    }
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email address";
    }
    // Combine phone parts
    $phoneFormatted = !empty($area) && !empty($phone) ? "($area) $phone" : "";
    if (!empty($ext)) {
        $phoneFormatted .= ", $ext";
    }
    // Combine contact types
    $contactTypes = implode(", ", $contactType);

    if (empty($errors)) {
        // Prepare insert query including user ID
        $query = "INSERT INTO cois3430_lab_contacts (userID, name, email, phoneType, phone, contactType, dateAdded)
                  VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$userID, $name, $email, $phoneType, $phoneFormatted, $contactTypes]);

        // Redirect after success
        header("Location: address.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Contacts</title>
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
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <div id="center-container">
            <h2>Add Contact Entry</h2>
            <form method="post" novalidate>
                <div class="form-item col">
                    <label for="name">Name</label>
                    <input id="name" type="text" name="name" value="<?= htmlspecialchars($name) ?>" />
                    <span class="error <?= empty($errors['name']) ? 'hidden' : '' ?>">
                        <?= $errors['name'] ?? '' ?>
                    </span>
                </div>
                <div class="form-item col">
                    <label for="email">Email Address</label>
                    <input id="email" type="email" name="email" value="<?= htmlspecialchars($email) ?>" />
                    <span class="error <?= empty($errors['email']) ? 'hidden' : '' ?>">
                        <?= $errors['email'] ?? '' ?>
                    </span>
                </div>
                <fieldset>
                    <legend>Phone Number</legend>
                    <div class="form-row">
                        <div class="form-item col">
                            <label for="phone-type">Number Type</label>
                            <select name="phone-type" id="phone-type">
                                <option value="">Type</option>
                                <option value="Mobile">Mobile</option>
                                <option value="Home">Home</option>
                                <option value="Work">Work</option>
                            </select>
                        </div>
                        <div class="form-item col">
                            <label for="area-code">Area Code</label>
                            <input id="area-code" type="text" name="area-code" value="<?= htmlspecialchars($area) ?>" />
                        </div>
                        <div class="form-item col">
                            <label for="phone">Phone</label>
                            <input id="phone" type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" />
                        </div>
                        <div class="form-item col">
                            <label for="ext">Ext</label>
                            <input id="ext" type="text" name="ext" value="<?= htmlspecialchars($ext) ?>" />
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Contact Type</legend>
                    <input type="checkbox" name="contact-type[]" value="friend"> Friend
                    <input type="checkbox" name="contact-type[]" value="family"> Family
                    <input type="checkbox" name="contact-type[]" value="coworker"> Co-worker
                    <input type="checkbox" name="contact-type[]" value="other"> Other
                </fieldset>
                <div>
                    <button type="submit">Submit</button>
                </div>
            </form>
        </div>
    </main>
    <footer>&copy; COIS 3430, Inc. 2024</footer>
</body>
</html>
