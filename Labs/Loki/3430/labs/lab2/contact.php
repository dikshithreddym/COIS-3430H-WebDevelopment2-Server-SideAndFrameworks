<?php

// Include the Contact class file
include_once 'includes/class_contact.php';

// Get contents of POST and assign default values using ??
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phoneType = $_POST['phone-type'] ?? '';
$areaCode = $_POST['area-code'] ?? '';
$phone = $_POST['phone'] ?? '';
$ext = $_POST['ext'] ?? '';
$contactType = $_POST['contact-type'] ?? [];

// Declare an errors array
$errors = [];

// Check if the form has been submitted
if (isset($_POST['submit'])) {
    // Validate the name field
    if (empty($name)) {
        $errors['name'] = true;
    }

    // Validate the email field
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = true;
    }

    // Validate phone fields
    if (!empty($phone)) {
        if (empty($phoneType)) {
            $errors['phoneType'] = true;
        }
        if (empty($areaCode) || !is_numeric($areaCode)) {
            $errors['areaCode'] = true;
        }
        if (!preg_match('/^\d{3}-\d{4}$/', $phone)) {
            $errors['phone'] = true;
        }
    }

    // If no errors, process the form
    if (empty($errors)) {
        // Start the session
        session_start();

        // Create a new Contact object
        $contact = new Contact($name, $email, $phoneType, $areaCode, $phone, $ext, $contactType);

        // Store the Contact object in the session
        if (!isset($_SESSION['contacts'])) {
            $_SESSION['contacts'] = [];
        }
        $_SESSION['contacts'][] = $contact;

        // Redirect to address.php and exit
        header('Location: address.php');
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
      </ul>
    </nav>
    <div id="center-container">
      <h2>Add Contact Entry</h2>
      <form method="post" novalidate>
        <div class="form-item col">
          <label for="name">Name</label>
          <!-- Make name sticky -->
          <input id="name" type="text" name="name" placeholder="Tom Jones" aria-required value="<?= htmlspecialchars($name) ?>" />
          <!-- Display error dynamically -->
          <span class="error <?= isset($errors['name']) ? '' : 'hidden' ?>">
            You must enter a name.
          </span>
        </div>
        <div class="form-item col">
          <label for="email">Email Address</label>
          <!-- Make email sticky -->
          <input id="email" type="email" name="email" placeholder="tom@nookinc.com" value="<?= htmlspecialchars($email) ?>" />
          <!-- Display error dynamically -->
          <span class="error <?= isset($errors['email']) ? '' : 'hidden' ?>">
            You must enter a valid email.
          </span>
        </div>
        <fieldset>
          <legend>Phone Number</legend>
          <div class="form-row">
            <div class="form-item col">
              <label for="phone-type" sr-only>Number Type</label>
              <select name="phone-type" id="phone-type">
                <option value="">Type</option>
                <option value="Mobile" <?= $phoneType === 'Mobile' ? 'selected' : '' ?>>Mobile</option>
                <option value="Home" <?= $phoneType === 'Home' ? 'selected' : '' ?>>Home</option>
                <option value="Work" <?= $phoneType === 'Work' ? 'selected' : '' ?>>Work</option>
              </select>
              <span class="error <?= isset($errors['phoneType']) ? '' : 'hidden' ?>">
                Phone type is required if a phone number is provided.
              </span>
            </div>
            <div class="form-item col">
              <label for="area-code">Area Code</label>
              <input id="area-code" type="text" name="area-code" size="3" maxlength="3" placeholder="705" value="<?= htmlspecialchars($areaCode) ?>" />
              <span class="error <?= isset($errors['areaCode']) ? '' : 'hidden' ?>">
                Area code must be numeric.
              </span>
            </div>
            <div class="form-item col">
              <label for="phone">Phone</label>
              <input id="phone" type="text" name="phone" size="8" maxlength="8" placeholder="748-1011" value="<?= htmlspecialchars($phone) ?>" />
              <span class="error <?= isset($errors['phone']) ? '' : 'hidden' ?>">
                Phone number must be in the format 123-4567.
              </span>
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
            <input type="checkbox" name="contact-type[]" id="friend" value="friend" <?= in_array('friend', $contactType) ? 'checked' : '' ?> />
            <label for="friend">Friend</label>
          </div>
          <div class="form-item row">
            <input type="checkbox" name="contact-type[]" id="family" value="family" <?= in_array('family', $contactType) ? 'checked' : '' ?> />
            <label for="family">Family</label>
          </div>
          <div class="form-item row">
            <input type="checkbox" name="contact-type[]" id="coworker" value="coworker" <?= in_array('coworker', $contactType) ? 'checked' : '' ?> />
            <label for="coworker">Co-worker</label>
          </div>
          <div class="form-item row">
            <input type="checkbox" name="contact-type[]" id="other" value="other" <?= in_array('other', $contactType) ? 'checked' : '' ?> />
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
