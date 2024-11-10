<?php
use App\Controllers\BookingController;

$bookingController = new BookingController();

// Create new booking
$router->post('/booking', function() use ($bookingController) {
    header("Content-Type: application/xml");
    
    try {
        // Get and parse XML input
        $xmlInput = file_get_contents('php://input');
        $xml = new SimpleXMLElement($xmlInput);
        
        // Extract values from XML
        $userId = (string)$xml->userId ?? null;
        $busId = (string)$xml->busId ?? null;
        $date = (string)$xml->date ?? null;
        $seatNumber = (string)$xml->seatNumber ?? null;
        
        echo $bookingController->createBooking($userId, $busId, $date, $seatNumber);
    } catch (Exception $e) {
        $response = new SimpleXMLElement('<?xml version="1.0"?><response></response>');
        $response->addChild('error', 'Invalid XML format: ' . $e->getMessage());
        echo $response->asXML();
    }
});

// Get booking details
$router->get('/booking/{id}', function($id) use ($bookingController) {
    header("Content-Type: application/xml");
    echo $bookingController->getBookingById($id);
});

// Get all bookings for a user
$router->get('/booking/user/{userId}', function($userId) use ($bookingController) {
    header("Content-Type: application/xml");
    echo $bookingController->getBookingsByUser($userId);
});

// Update booking status
$router->put('/booking/{id}', function($id) use ($bookingController) {
    header("Content-Type: application/xml");
    
    try {
        $xmlInput = file_get_contents('php://input');
        $xml = new SimpleXMLElement($xmlInput);
        $status = (string)$xml->status ?? null;
        
        echo $bookingController->updateBookingStatus($id, $status);
    } catch (Exception $e) {
        $response = new SimpleXMLElement('<?xml version="1.0"?><response></response>');
        $response->addChild('error', 'Invalid XML format: ' . $e->getMessage());
        echo $response->asXML();
    }
});

// Cancel booking
$router->delete('/booking/{id}', function($id) use ($bookingController) {
    header("Content-Type: application/xml");
    echo $bookingController->cancelBooking($id);
});