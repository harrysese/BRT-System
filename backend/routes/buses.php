<?php
use App\controllers\BusController;

$busController = new BusController();

// Route to create a new bus
$router->post('/bus', function() use ($busController) {
    // Get JSON input data
    $data = json_decode(file_get_contents('php://input'), true);
    $busNumber = $data['busNumber'] ?? null;
    $capacity = $data['capacity'] ?? null;
    $routeId = $data['routeId'] ?? null;

    // Call createBus method and output response
    echo $busController->createBus($busNumber, $capacity, $routeId);
});

// Route to get a bus by ID
$router->get('/bus/{id}', function($id) use ($busController) {
    echo $busController->getBusById($id);
});

// Route to get all buses by route ID
$router->get('/buses/route/{routeId}', function($routeId) use ($busController) {
    echo $busController->getBusesByRouteId($routeId);
});

// Route to update a bus
$router->put('/bus/{id}', function($id) use ($busController) {
    // Get JSON input data
    $data = json_decode(file_get_contents('php://input'), true);
    $busNumber = $data['busNumber'] ?? null;
    $capacity = $data['capacity'] ?? null;
    $routeId = $data['routeId'] ?? null;

    // Call updateBus method and output response
    echo $busController->updateBus($id, $busNumber, $capacity, $routeId);
});

// Route to delete a bus
$router->delete('/bus/{id}', function($id) use ($busController) {
    echo $busController->deleteBus($id);
});
