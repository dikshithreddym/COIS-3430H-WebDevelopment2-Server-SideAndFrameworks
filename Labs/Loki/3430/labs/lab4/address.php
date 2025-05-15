<?php
session_start(); // Start session

// Redirect if not logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/includes/library.php'; // Include database connection
$pdo = connectDB();

$userID = $_SESSION['userID'];
$search = $_GET['search'] ?? "";

// Prepare query to fetch only the logged-in user's contacts
if (!empty($search)) {
    $query = "SELECT name, email, phoneType, phone, contactType FROM cois3430_lab_contacts WHERE userID = ? AND name LIKE ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userID, "%$search%"]);
} else {
    $query = "SELECT name, email, phoneType, phone, contactType FROM cois3430_lab_contacts WHERE userID = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userID]);
}
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Address Book</title>
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
      <h2><?= htmlspecialchars($_SESSION['username']) ?>'s Entries</h2>
      <table id="results">
        <thead>
          <th>Name</th>
          <th>Email</th>
          <th>P. Type</th>
          <th>Phone Number</th>
          <th>Contact Type</th>
        </thead>
        <tbody>
          <?php foreach ($contacts as $contact) : ?>
            <tr>
              <td><?= htmlspecialchars($contact['name']) ?></td>
              <td><?= htmlspecialchars($contact['email']) ?></td>
              <td><?= htmlspecialchars($contact['phoneType']) ?></td>
              <td><?= htmlspecialchars($contact['phone']) ?></td>
              <td><?= htmlspecialchars($contact['contactType']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div id="right-side-container">
      <h2>Filter Results</h2>
      <form method="get">
        <label for="search">Name:</label>
        <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Filter Address Book</button>
      </form>
    </div>
  </main>
  <footer>&copy; COIS 3430, Inc. 2024</footer>
</body>
</html>
