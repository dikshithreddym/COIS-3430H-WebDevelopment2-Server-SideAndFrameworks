<?php
include 'config.php';
header("Content-Type: application/json");

$apiKey = WEATHERSTACK_API_KEY; // weatherstack key

$lat = $_GET['lat'] ?? null;
$lon = $_GET['lon'] ?? null;
$city = $_GET['city'] ?? null;

if ($lat && $lon) {
    $query = "$lat,$lon";
} elseif ($city) {
    $query = urlencode($city);
} else {
    $query = "fetch:ip"; // fallback to IP-based location
}

$url = "http://api.weatherstack.com/current?access_key=$apiKey&query=$query&units=m";

$response = file_get_contents($url);
if ($response === false) {
    echo json_encode(["status" => "error", "message" => "Unable to contact Weatherstack API"]);
    exit();
}

$data = json_decode($response, true);

if (isset($data["success"]) && $data["success"] === false) {
    echo json_encode([
        "status" => "error",
        "message" => $data["error"]["info"] ?? "Unknown API error"
    ]);
    exit();
}

echo json_encode([
    "status" => "success",
    "city" => $data["location"]["name"],
    "country" => $data["location"]["country"],
    "temperature" => $data["current"]["temperature"],
    "description" => $data["current"]["weather_descriptions"][0],
    "icon" => $data["current"]["weather_icons"][0]
]);
?>
