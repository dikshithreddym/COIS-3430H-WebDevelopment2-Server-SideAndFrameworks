<?php

session_start();
if (!isset($_SESSION['userid'])) {
  header("Location:login.php");
  exit();
}

$userid = $_SESSION['userid'];

//get data from post
$name = $_POST['name'] ?? "";
$email = $_POST['email'] ?? "";
$phoneType = $_POST['phone-type'] ?? "";
$area = $_POST['area-code'] ?? "";
$phone = $_POST['phone'] ?? "";
$ext = $_POST['ext'] ?? "";
$contactType = $_POST['contact-type'] ?? array();
$errors = array();



//when form has been submitted
if (isset($_POST['submit'])) {

  //include connection function
  require_once './includes/library.php';
  $pdo = connectdb(); //create database connection

  //make sure name isn't empty
  if (empty($name)) {
    $errors['name'] = true;
  }
  //verify email is valid
  if (empty(filter_var($email, FILTER_VALIDATE_EMAIL))) {
    $errors['email'] = true;
  }
  //combine phone parts into one number
  if (!empty($area) && !empty($phone))
    $phone = "(" . $area . ") " . $phone;
  else {
    $phone = "";
  }

  if (!empty($ext) && !empty($phone)) {
    $phone .= (", " . $ext);
  }

  //combine contact types into comma seperated string
  $contactTypes = join(", ", $contactType);

  if (empty($errors)) {

    //create query
    $insert = "INSERT INTO
            `cois3430_lab_contacts` (`userID`,`name`, `email`, `phoneType`, `phone`, `contactType`, `dateAdded`)
            VALUES (?,?, ?, ?, ?, ?, NOW())";
    $insert = $pdo->prepare($insert); //prepare query
    //execute query
    $success = $insert->execute([
      $userid,
      $name,
      $email,
      $phoneType,
      $phone,
      $contactTypes,
    ]);

    //redirect user
    header("Location:address.php");
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
          <input id="name" type="name" name="name" placeholder="Tom Jones" aria-required value="<?= $name ?>" />
          <span class="error <?= !isset($errors['name']) ? 'hidden' : '' ?>">
            You must enter a name.
          </span>
        </div>
        <div class="form-item col">
          <label for="email">Email Address</label>
          <input id="email" type="email" name="email" placeholder="tom@nookinc.com" value="<?= $email ?>" />
          <span class="error <?= !isset($errors['email']) ? 'hidden' : '' ?>">
            You must enter an email.
          </span>

        </div>
        <fieldset>
          <legend>Phone Number</legend>
          <div class="form-row">
            <div class="form-item col">
              <label for="phone-type" sr-only>Number Type</label>
              <select name="phone-type" id="phone-type">
                <option value="">Type</option>
                <option value="Mobile">Mobile</option>
                <option value="Home">Home</option>
                <option value="Work">Work</option>
              </select>
            </div>
            <div class="form-item col">
              <label for="area-code">Area Code</label>
              <input id="area-code" type="area-code" name="area-code" size="3" maxlength="3" placeholder="705" />
            </div>
            <div class="form-item col">
              <label for="phone">Phone</label>
              <input id="phone" type="phone" name="phone" size="8" maxlength="8" placeholder="748-1011" />
            </div>
            <div class="form-item col">
              <label for="ext">Ext</label>
              <input id="ext" type="ext" name="ext" size="4" maxlength="10" placeholder="1559" />
            </div>
          </div>
        </fieldset>
        <fieldset>
          <legend>Contact Type</legend>
          <div class="form-item row">
            <input type="checkbox" name="contact-type[]" id="friend" value="friend" />
            <label for="friend">Friend</label>
          </div>
          <div class="form-item row">
            <input type="checkbox" name="contact-type[]" id="family" value="family" />
            <label for="family">Family</label>
          </div>
          <div class="form-item row">
            <input type="checkbox" name="contact-type[]" id="coworker" value="coworker" />
            <label for="coworker">Co-worker</label>
          </div>
          <div class="form-item row">
            <input type="checkbox" name="contact-type[]" id="other" value="other" />
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