<?php
/**
 * Router - Handles URL routing
 */
class Router {
    private $routes = [];

    public function get($path, $controller, $method) {
        $this->addRoute('GET', $path, $controller, $method);
    }

    public function post($path, $controller, $method) {
        $this->addRoute('POST', $path, $controller, $method);
    }

    private function addRoute($httpMethod, $path, $controller, $method) {
        $this->routes[] = [
            'method' => $httpMethod,
            'path' => $path,
            'controller' => $controller,
            'action' => $method
        ];
    }

    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // Remove query string
        $requestUri = strtok($requestUri, '?');
        
        // Remove base URL
        $requestUri = str_replace(BASE_URL, '', $requestUri);
        $requestUri = '/' . trim($requestUri, '/');
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = $this->convertToRegex($route['path']);
            
            if (preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches); // Remove full match
                
                $controllerName = $route['controller'];
                $action = $route['action'];
                
                require_once CONTROLLER_PATH . '/' . $controllerName . '.php';
                $controller = new $controllerName();
                
                call_user_func_array([$controller, $action], $matches);
                return;
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        echo "404 - Página no encontrada";
    }

    private function convertToRegex($path) {
        // Convert {id} to regex capturing group
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $path);
        return '#^' . $pattern . '$#';
    }
}
