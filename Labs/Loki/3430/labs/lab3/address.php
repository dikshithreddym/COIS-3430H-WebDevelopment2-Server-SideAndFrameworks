<?php
# Include library and create connection
require_once 'includes/library.php';
$pdo = connectdb(); // Connect to the database

# Retrieve the search parameter
$search = $_GET['search'] ?? '';

# Prepare the SQL query based on whether a search term is provided
if (!empty($search)) {
  // Use a wildcard search with prepared statement
  $sql = "SELECT name, email, phoneType, phone, contactType 
          FROM cois3430_lab_contacts 
          WHERE name LIKE ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(["%{$search}%"]);
} else {
  // If no search term, retrieve all rows
  $sql = "SELECT name, email, phoneType, phone, contactType FROM cois3430_lab_contacts";
  $stmt = $pdo->query($sql);
}
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
      </ul>
    </nav>
    <div id="center-container">
      <h2>Entries</h2>
      <table id="results">
        <thead>
          <th>Name</th>
          <th>Email</th>
          <th>P. Type</th>
          <th>Phone Number</th>
          <th>Contact Type</th>
        </thead>
        <tbody>
          <!-- Begin dynamic loop -->
          <?php foreach ($stmt as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= htmlspecialchars($row['phoneType']) ?></td>
              <td><?= htmlspecialchars($row['phone']) ?></td>
              <td><?= htmlspecialchars($row['contactType']) ?></td>
            </tr>
          <?php endforeach; ?>
          <!-- End dynamic loop -->
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
  <footer>&copy; COIS 3430, Inc. 2024 &mdash; Built by {{ name }}</footer>
</body>

</html>
