<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$dbHost = $_ENV['DB_HOST'];
$dbPort = $_ENV['DB_PORT'];
$dbName = $_ENV['DB_NAME'];
$dbUser = $_ENV['DB_USER'];
$dbPassword = $_ENV['DB_PASSWORD'];

return [
    'host' => $dbHost,             // Hostname
    'port' => $dbPort,                  // Port number
    'dbname' => $dbName,  // Database name
    'user' => $dbUser,         // Database username
    'password' => $dbPassword,     // Database password
];
