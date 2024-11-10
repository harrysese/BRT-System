<?php
use App\controllers\userController;

$userController = new userController();

$router->get('/user/{id}', function($id) use ($userController) {
    header("Content-Type: application/xml");
    echo $userController->getUserById($id);
});

$router->post('/user/register', function() use ($userController) {
    header("Content-Type: application/xml");
    
    try {
        // Get and parse XML input
        $xmlInput = file_get_contents('php://input');
        $xml = new SimpleXMLElement($xmlInput);
        
        // Extract values from XML
        $username = (string)$xml->username ?? null;
        $email = (string)$xml->email ?? null;
        $password = (string)$xml->password ?? null;
        
        // Call register method and output response
        echo $userController->register($username, $email, $password);
    } catch (Exception $e) {
        $response = new SimpleXMLElement('<?xml version="1.0"?><response></response>');
        $response->addChild('error', 'Invalid XML format: ' . $e->getMessage());
        echo $response->asXML();
    }
});

$router->post('/user/login', function() use($userController) {
    header("Content-Type: application/xml");
    
    try {
        // Get and parse XML input
        $xmlInput = file_get_contents('php://input');
        $xml = new SimpleXMLElement($xmlInput);
        
        // Extract values from XML
        $username = (string)$xml->username ?? null;
        $password = (string)$xml->password ?? null;
        
        echo $userController->authenticateUser($username, $password);
    } catch (Exception $e) {
        $response = new SimpleXMLElement('<?xml version="1.0"?><response></response>');
        $response->addChild('error', 'Invalid XML format: ' . $e->getMessage());
        echo $response->asXML();
    }
});

$router->put('/user/{id}', function($id) use ($userController) {
    header("Content-Type: application/xml");
    
    try {
        // Get and parse XML input
        $xmlInput = file_get_contents('php://input');
        $xml = new SimpleXMLElement($xmlInput);
        
        // Extract values from XML
        $username = (string)$xml->username ?? null;
        $email = (string)$xml->email ?? null;
        $password = (string)$xml->password ?? null;
        
        echo $userController->updateUser($id, $username, $password, $email);
    } catch (Exception $e) {
        $response = new SimpleXMLElement('<?xml version="1.0"?><response></response>');
        $response->addChild('error', 'Invalid XML format: ' . $e->getMessage());
        echo $response->asXML();
    }
});

$router->delete('/user/{id}', function($id) use ($userController) {
    header("Content-Type: application/xml");
    echo $userController->deleteUser($id);
});