<?php

//get data from post
$username = $_POST['username'] ?? "";
$password = $_POST['password'] ?? "";
$errors = array();

//when form has been submitted
if (isset($_POST['submit'])) {
  require_once("./includes/library.php");
  $pdo = connectdb();
  $query = "select userID, pwd from cois3430_lab_users where username=?";

  $stmt = $pdo->prepare($query);
  $stmt->execute([$username]);
  $dbrow = $stmt->fetch();

  if ($dbrow) {
    if (password_verify($password, $dbrow['pwd'])) {
      session_start();
      $_SESSION['username'] = $username;
      $_SESSION['userid'] = $dbrow['userID'];
      header("Location: address.php");
      exit();
    } else {
      $errors['password'] = true;
    }
  } else {
    $errors['username'] = true;
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
      <form id="login" method="post" action="" />
      <div class="form-item col">
        <label for="username">Username:</label>
        <!--notice the echo of username to allow for a sticky form on error-->
        <input type="text" id="username" name="username" size="25" value="<?php echo $username ?>" />
        <span class="error <?= !isset($errors['username']) ? 'hidden' : '' ?>">Your username was invalid</span>
      </div>
      <div class="form-item col">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" size="25" />
        <span class="error <?= !isset($errors['password']) ? 'hidden' : '' ?>">Your passwords was invalid</span>
      </div>

      <div class="form-item row">
        <label for="remember">Remember:</label>
        <input type="checkbox" name="remember" value="remember" />
      </div>

      <button id="submit" name="submit" class="centered">Login</button>
      <a href="create-account.php" class="centered">Create a New Account</a>
      </form>

    </div>

    <div id="right-side-container"></div>
  </main>

  <footer>&copy; COIS 3430, Inc. 2024 &mdash; Built by {{ name }}</footer>


</body>

</html>