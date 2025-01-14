<?php
namespace App;

class Router {
    private $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function put($path, $callback){
        $this->routes['PUT'][$path] = $callback;
    }

    public function delete($path, $callback){
        $this->routes['DELETE'][$path] = $callback;
    }


    public function resolve($method, $path) {
        // Check exact match first
        if (isset($this->routes[$method][$path])) {
            call_user_func($this->routes[$method][$path]);
            return;
        }
    
        // Check dynamic routes
        foreach ($this->routes[$method] as $route => $callback) {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $route);
            if (preg_match("#^$pattern$#", $path, $matches)) {
                array_shift($matches);  // Remove the full match
                call_user_func_array($callback, $matches);  // Pass route parameters
                return;
            }
        }
    
        // 404 if no match found
        http_response_code(404);
        echo "404 - Not Found";
    }
    
}
