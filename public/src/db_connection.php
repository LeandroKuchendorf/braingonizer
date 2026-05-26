<?php

// Load .env file from project root if it exists
$envFile = __DIR__ . '/../../../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

$host     = $_ENV['DB_HOST']     ?? getenv('DB_HOST')     ?: 'localhost';
$dbname   = $_ENV['DB_NAME']     ?? getenv('DB_NAME')     ?: '';
$username = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: '';
$password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';

if (empty($dbname) || empty($username)) {
    die("Database configuration missing. Please copy .env.example to .env and fill in your credentials.");
}

$db_connected = false;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db_connected = true;
} catch (PDOException $e) {
    die("Database connection failed. Check your .env configuration.");
}
