<?php
use App\Router;

$router = new Router();

// Load route files
require_once __DIR__ . '/users.php';
require_once __DIR__ . '/buses.php';
require_once __DIR__ . '/booking.php';


// Add other modules as needed...
