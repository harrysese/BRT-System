<?php
// Autoload dependencies installed via Composer
require __DIR__ . '/../vendor/autoload.php';

// Load environment variables from the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Set up Monolog for logging
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use App\Router;
use App\controllers\userController;
use App\controllers\busController;
$log = new Logger('my-php-backend');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::DEBUG));

// Log the start of the application
$log->info('Application has started');

// Include main API route file
require_once __DIR__ . '/../routes/api.php';

// Get the current HTTP method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Resolve the request
$router->resolve($method, $path);

