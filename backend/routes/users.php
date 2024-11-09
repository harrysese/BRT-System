<?php
use App\controllers\userController;

$userController = new userController();

$router->get('/user/{id}', function($id) use ($userController) {
    $data = json_decode(file_get_contents('php://input'), true);
    echo $userController->getUserById($id);
});

$router->post('/user/register', function() use ($userController) {
    // Get JSON input data
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? null;
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    // Call register method and output response
    echo $userController->register($username, $email, $password);
});

$router->post('/user/login', function() use($userController) {
    // Get JSON input data
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? null;
    $password = $data['password'] ?? null;

    echo $userController->authenticateUser($username, $password);
});

$router->put('/user/{id}', function($id) use ($userController) {
    // Get JSON input data
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? null;
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    // Call register method and output response
    echo $userController->updateUser($id , $username, $password, $email);
});

$router->delete('/user/{id}', function($id) use ($userController) {
    // Get JSON input data
    $data = json_decode(file_get_contents('php://input'), true);

    // Call register method and output response
    echo $userController->deleteUser($id , $username, $password, $email);
});