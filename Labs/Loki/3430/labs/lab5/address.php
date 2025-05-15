<?php

# Include library and create connection
require_once './includes/library.php';
$pdo = connectDB();
session_start();
if (!isset($_SESSION['userid'])) {
  header("Location:login.php");
  exit();
}

$userid = $_SESSION['userid'];

//get search value from url
$search = $_GET['search'] ?? "";

//if a search value was provided include it in query
if (!empty($search)) {
  $contacts = "SELECT
        name,
        email,
        phoneType,
        phone,
        contactType
    FROM cois3430_lab_contacts WHERE userID=? and name LIKE ?";
  $contacts = $pdo->prepare($contacts);
  $contacts->execute([$userid, '%' . $search . '%']);
} else { //otherwise run simple query
  $contacts = "SELECT
        name,
        email,
        phoneType,
        phone,
        contactType
    FROM cois3430_lab_contacts
    WHERE userID = ?";
  $contacts = $pdo->prepare($contacts);
  $contacts->execute([$userid]);
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
          <?php foreach ($contacts as $contact) : ?>
            <tr>
              <td><?= $contact['name'] ?></td>
              <td><?= $contact['email'] ?></td>
              <td><?= $contact['phoneType'] ?></td>
              <td><?= $contact['phone'] ?></td>
              <td><?= $contact['contactType'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div id="right-side-container">
      <h2>Filter Results</h2>
      <form method="get">
        <label for="search">Name:</label>
        <input type="text" name="search" id="search" value="<?= $search ?>">
        <button type="submit">Filter Address Book</button>
      </form>
    </div>
  </main>
  <footer>&copy; COIS 3430, Inc. 2024 &mdash; Built by {{ name }}</footer>

</body>

</html>