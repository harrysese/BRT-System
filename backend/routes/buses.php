<?php
use App\controllers\BusController;

$busController = new BusController();

// Route to create a new bus
$router->post('/bus', function() use ($busController) {
    // Set header for XML response
    header("Content-Type: application/xml");

    // Get XML input data
    $xmlInput = file_get_contents('php://input');
    
    try {
        // Parse XML input
        $xml = new SimpleXMLElement($xmlInput);
        
        // Extract values from XML
        $busNumber = (string)$xml->busNumber ?? null;
        $capacity = (string)$xml->capacity ?? null;
        $routeId = (string)$xml->routeId ?? null;
        
        // Debug: Log the extracted values
        error_log("Extracted values - busNumber: $busNumber, capacity: $capacity, routeId: $routeId");
        
        // Call createBus method and output response in XML
        echo $busController->createBus($busNumber, $capacity, $routeId);
    } catch (Exception $e) {
        // Handle invalid XML
        $response = new SimpleXMLElement('<?xml version="1.0"?><response></response>');
        $response->addChild('error', 'Invalid XML format: ' . $e->getMessage());
        echo $response->asXML();
    }
});
// Route to get a bus by ID
$router->get('/bus/{id}', function($id) use ($busController) {
    // Set header for XML response
    header("Content-Type: application/xml");

    // Output XML response
    echo $busController->getBusById($id);
});

// Route to get all buses by route ID
$router->get('/buses/route/{routeId}', function($routeId) use ($busController) {
    // Set header for XML response
    header("Content-Type: application/xml");

    // Output XML response
    echo $busController->getBusesByRouteId($routeId);
});

$router->put('/bus/{id}', function($id) use ($busController) {
    // Set header for XML response
    header("Content-Type: application/xml");

    // Get XML input data
    $xmlInput = file_get_contents('php://input');
    
    try {
        // Parse XML input
        $xml = new SimpleXMLElement($xmlInput);
        
        // Extract values from XML
        $busNumber = (string)$xml->busNumber ?? null;
        $capacity = (string)$xml->capacity ?? null;
        $routeId = (string)$xml->routeId ?? null;
        
        // Output XML response
        echo $busController->updateBus($id, $busNumber, $capacity, $routeId);
    } catch (Exception $e) {
        // Handle invalid XML
        $response = new SimpleXMLElement('<?xml version="1.0"?><response></response>');
        $response->addChild('error', 'Invalid XML format: ' . $e->getMessage());
        echo $response->asXML();
    }
});

// Route to delete a bus
$router->delete('/bus/{id}', function($id) use ($busController) {
    // Set header for XML response
    header("Content-Type: application/xml");

    // Output XML response
    echo $busController->deleteBus($id);
});
