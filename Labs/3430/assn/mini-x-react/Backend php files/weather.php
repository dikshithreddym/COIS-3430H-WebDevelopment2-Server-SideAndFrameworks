<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$lat = $_GET['lat'] ?? null;
$lon = $_GET['lon'] ?? null;

if (!$lat || !$lon) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Latitude and longitude are required.']);
    exit;
}

$apiKey = WEATHERSTACK_API_KEY;
$url = "http://api.weatherstack.com/current?access_key=$apiKey&query=$lat,$lon";

$response = @file_get_contents($url);
if (!$response) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Weather API request failed.']);
    exit;
}

$weatherData = json_decode($response, true);

if (isset($weatherData['current']) && isset($weatherData['location'])) {
    echo json_encode([
        'success' => true,
        'location' => $weatherData['location']['name'],
        'temperature' => $weatherData['current']['temperature'],
        'weather_descriptions' => $weatherData['current']['weather_descriptions'],
        'icon' => $weatherData['current']['weather_icons'][0] ?? null
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not retrieve weather data.']);
}
