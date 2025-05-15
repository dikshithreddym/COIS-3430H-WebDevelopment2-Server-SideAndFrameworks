<?php
header("Content-Type: application/json");

// Include the database connection and helper functions
require_once __DIR__ . "/../includes/library.php";

// Function to send JSON response
function json_response($status, $data) {
  header("Content-Type: application/json");
  http_response_code($status);
  echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
  exit;
}

// Get the requested URL and method
$request_uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Define base API path
$base_path = "/~dmacherla/3430/labs/lab5/api/";
$path = str_replace($base_path, "", parse_url($request_uri, PHP_URL_PATH));

// Routing
if ($method === "GET" && $path === "contacts") {
  // ✅ Fetch all contacts from the database
  try {
      $pdo = connectdb(); // Ensure database connection is working
      $stmt = $pdo->query("SELECT * FROM cois3430_lab_contacts"); // Fetch all contacts
      $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC); // Convert to array

      // ✅ Send JSON response with contacts
      json_response(200, $contacts);
  } catch (Exception $e) {
      json_response(500, ["error" => "Database error: " . $e->getMessage()]);
  }

} elseif ($method === "PATCH" && $path === "users") {
    //  Step 1: Get API key from headers
    $headers = getallheaders();
    $apiKey = $headers["X-API-Key"] ?? null;

    //  Step 2: Check if API key exists
    if (!$apiKey) {
        json_response(400, ["error" => "You must provide an API key."]);
    }

    // Step 3: Get JSON input data
    $input = json_decode(file_get_contents("php://input"), true);
    $email = $input['email'] ?? null;

    //  Step 4: Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_response(400, ["error" => "You must provide a valid email address."]);
    }

    try {
        $pdo = connectdb();

        //  Step 5: Verify API key exists
        $stmt = $pdo->prepare("SELECT 1 FROM cois3430_lab_users WHERE apikey = ?");
        $stmt->execute([$apiKey]);
        if (!$stmt->fetch()) {
            json_response(400, ["error" => "The provided API key is invalid."]);
        }

        // Step 6: Update the user's email
        $stmt = $pdo->prepare("UPDATE cois3430_lab_users SET email = ? WHERE apikey = ?");
        $stmt->execute([$email, $apiKey]);

        // Step 7: Return success response
        json_response(200, ["message" => "Update successful"]);
    } catch (Exception $e) {
        json_response(500, ["error" => "Database error: " . $e->getMessage()]);
    }
} else {
    // Fallback for unsupported requests
    json_response(404, ["error" => "We are unable to respond to this request."]);
}
?>
