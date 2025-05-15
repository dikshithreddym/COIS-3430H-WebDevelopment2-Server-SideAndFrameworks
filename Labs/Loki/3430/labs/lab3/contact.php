<?php
require_once 'includes/library.php'; // Include library.php for the database connection function.

$name = $_POST['name'] ?? "";
$email = $_POST['email'] ?? "";
$phoneType = $_POST['phone-type'] ?? "";
$area = $_POST['area-code'] ?? "";
$phone = $_POST['phone'] ?? "";
$ext = $_POST['ext'] ?? "";
$contactType = $_POST['contact-type'] ?? array();
$errors = array();

if (isset($_POST['submit'])) {

  // Validate name
  if (empty($name)) {
    $errors['name'] = true;
  }

  // Validate email
  if (empty(filter_var($email, FILTER_VALIDATE_EMAIL))) {
    $errors['email'] = true;
  }

  // Format phone number
  if (!empty($area) && !empty($phone))
    $phone = "(" . $area . ") " . $phone;
  else {
    $phone = "";
  }

  if (!empty($ext) && !empty($phone)) {
    $phone .= (", " . $ext);
  }

  // Combine contact types into a single string
  $contactTypes = join(", ", $contactType);

  // Check for duplicate email in the database
  if (empty($errors)) {
    $pdo = connectdb(); // Create a PDO connection to the database

    $emailCheckQuery = "SELECT COUNT(*) FROM cois3430_lab_contacts WHERE email = ?";
    $stmt = $pdo->prepare($emailCheckQuery);
    $stmt->execute([$email]);
    $emailExists = $stmt->fetchColumn() > 0;

    if ($emailExists) {
      $errors['emailExists'] = "This email address is already in use.";
    }
  }

  // If no errors, process the data
  if (empty($errors)) {
    $sql = "INSERT INTO cois3430_lab_contacts (name, email, phoneType, phone, contactType, dateAdded) 
            VALUES (?, ?, ?, ?, ?, NOW())";

    try {
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$name, $email, $phoneType, $phone, $contactTypes]);
    } catch (PDOException $e) {
      die("Error inserting data: " . $e->getMessage());
    }

    // Redirect to address.php after successful insertion
    header("Location: address.php");
    exit();
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
      </ul>
    </nav>
    <div id="center-container">
      <h2>Add Contact Entry</h2>
      <form method="post" novalidate>
        <div class="form-item col">
          <label for="name">Name</label>
          <input id="name" type="text" name="name" placeholder="Tom Jones" aria-required value="<?= htmlspecialchars($name) ?>" />
          <span class="error <?= !isset($errors['name']) ? 'hidden' : '' ?>">
            You must enter a name.
          </span>
        </div>
        <div class="form-item col">
          <label for="email">Email Address</label>
          <input id="email" type="email" name="email" placeholder="tom@nookinc.com" value="<?= htmlspecialchars($email) ?>" />
          <span class="error <?= !isset($errors['email']) ? 'hidden' : '' ?>">
            You must enter a valid email.
          </span>
          <span class="error <?= !isset($errors['emailExists']) ? 'hidden' : '' ?>">
            <?= htmlspecialchars($errors['emailExists'] ?? '') ?>
          </span>
        </div>
        <fieldset>
          <legend>Phone Number</legend>
          <div class="form-row">
            <div class="form-item col">
              <label for="phone-type">Number Type</label>
              <select name="phone-type" id="phone-type">
                <option value="">Type</option>
                <option value="Mobile" <?= $phoneType == "Mobile" ? "selected" : "" ?>>Mobile</option>
                <option value="Home" <?= $phoneType == "Home" ? "selected" : "" ?>>Home</option>
                <option value="Work" <?= $phoneType == "Work" ? "selected" : "" ?>>Work</option>
              </select>
            </div>
            <div class="form-item col">
              <label for="area-code">Area Code</label>
              <input id="area-code" type="text" name="area-code" size="3" maxlength="3" placeholder="705" value="<?= htmlspecialchars($area) ?>" />
            </div>
            <div class="form-item col">
              <label for="phone">Phone</label>
              <input id="phone" type="text" name="phone" size="8" maxlength="8" placeholder="748-1011" value="<?= htmlspecialchars($phone) ?>" />
            </div>
            <div class="form-item col">
              <label for="ext">Ext</label>
              <input id="ext" type="text" name="ext" size="4" maxlength="10" placeholder="1559" value="<?= htmlspecialchars($ext) ?>" />
            </div>
          </div>
        </fieldset>
        <fieldset>
          <legend>Contact Type</legend>
          <div class="form-item row">
            <input type="checkbox" name="contact-type[]" id="friend" value="friend" <?= in_array("friend", $contactType) ? "checked" : "" ?> />
            <label for="friend">Friend</label>
          </div>
          <div class="form-item row">
            <input type="checkbox" name="contact-type[]" id="family" value="family" <?= in_array("family", $contactType) ? "checked" : "" ?> />
            <label for="family">Family</label>
          </div>
          <div class="form-item row">
            <input type="checkbox" name="contact-type[]" id="coworker" value="coworker" <?= in_array("coworker", $contactType) ? "checked" : "" ?> />
            <label for="coworker">Co-worker</label>
          </div>
          <div class="form-item row">
            <input type="checkbox" name="contact-type[]" id="other" value="other" <?= in_array("other", $contactType) ? "checked" : "" ?> />
            <label for="other">Other</label>
          </div>
        </fieldset>
        <div>
          <button id="submit" type="submit" name="submit">Submit</button>
        </div>
      </form>
    </div>
    <div id="right-side-container"></div>
  </main>
  <footer>&copy; COIS 3430, Inc. 2024 &mdash; Built by {{ name }}</footer>
</body>

</html>
