<?php
// Step 1: Split the string into an array
$names = "Harry Potter, ron Weasley, Hermione Granger, lavender brown, Pavarti patil, NEVILLE Longbottom, Seamus FiNNegan, Dean Thomas";
$arrayNames = explode(", ", $names); // Split the string
var_dump($arrayNames); // Test if splitting worked

// Step 2: Add "Draco Malfoy" to the array
$arrayNames[] = "Draco Malfoy"; // Add new name
var_dump($arrayNames); // Test if addition worked

// Step 3: Sort the array in a case-insensitive manner
natcasesort($arrayNames); // Sort array, preserving keys
var_dump($arrayNames); // Test sorting
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>COIS 3430 Lab 1</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <h1>Welcome to COIS 3430</h1>
  <ul>
    <?php foreach ($arrayNames as $name): ?>
      <?php 
      // Ensure proper title case
      $formattedName = ucwords(strtolower($name)); 
      // Check if name contains "h" (case insensitive)
      $className = stripos($name, 'h') !== false ? 'gryffindor-red' : 'ravenclaw-blue';
      ?>
      <li class="<?= $className ?>"><?= $formattedName ?></li>
    <?php endforeach; ?>
  </ul>
</body>

</html>
