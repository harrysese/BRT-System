<?php
// Autoload dependencies installed via Composer
require __DIR__ . '/../vendor/autoload.php';

// Load environment variables from the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Set up Monolog for logging
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('my-php-backend');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::DEBUG));

// Log the start of the application
$log->info('Application has started');

// Parse the current URI
$uri = trim($_SERVER['REQUEST_URI'], '/');

// Basic Routing
if ($uri === '') {
    // Route: Home
    echo "Welcome to the PHP Backend!";
    $log->info("Accessed the home route");
} elseif ($uri === 'api/hello') {
    // Route: API Hello
    header('Content-Type: application/json');
    echo json_encode(["message" => "Hello, World!"]);
    $log->info("Accessed the /api/hello route");
} else {
    // Route: 404 Not Found
    header('Content-Type: application/json', true, 404);
    echo json_encode(["error" => "Route not found"]);
    $log->warning("Attempted to access an unknown route: /{$uri}");
}
