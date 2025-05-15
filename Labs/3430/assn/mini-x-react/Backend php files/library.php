<?php
require_once __DIR__ . '/config.php';

// Define DOCROOT and WEBROOT for dynamic paths (optional)
$direx = explode('/', getcwd());
define('DOCROOT', "/$direx[1]/$direx[2]/");
define('WEBROOT', "/$direx[1]/$direx[2]/$direx[3]/");

function connectDB()
{
    $configPath = DOCROOT . "pwd/config.ini";

    if (!file_exists($configPath)) {
        header('Content-Type: application/json');
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'error' => 'Config file not found'
        ]));
    }

    $config = parse_ini_file($configPath);

    $dsn = "mysql:host={$config['domain']};dbname={$config['dbname']};charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        die(json_encode([
            'success' => false,
            'error' => 'Database connection failed: ' . $e->getMessage()
        ]));
    }
}

$pdo = connectdb();
