<?php
session_start();
header('Content-Type: application/json');

// Clear session data
$_SESSION = [];
session_unset();
session_destroy();

$response = ['success' => true, 'message' => 'Logout successful'];
echo json_encode($response);
