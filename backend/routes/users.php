<?php
use App\controllers\userController;

$userController = new userController();

$router->post('/api/register', function() use ($userController) {
    // Get JSON input data
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? null;
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    // Call register method and output response
    echo $userController->register($username, $email, $password);
});
