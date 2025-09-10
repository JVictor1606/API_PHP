<?php

class Route
{
    public function dispatch()
    {
        $routes = require './Routes/route.php';
        $uri = $_SERVER['PATH_INFO'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        if (!method_exists($class, $metodo)) {
            echo 'Metodo nao existe';
            return;
        }

        foreach ($routes[$method] as $route => $action) {
            $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
            $pattern = "#^$pattern$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                $class = $action[0];
                $method = $action[1];

                if (!class_exists($class)) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Controller não existe']);
                    return;
                }

                if (!method_exists($class, $method)) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Método não existe']);
                    return;
                }

                call_user_func_array([new $class, $method], $matches);
                return;
            }
        
        }

        if (empty($routes[$method][$uri])) {
            echo 'rota nao existe';
            return;
        }


        call_user_func_array([new $class, $metodo], []);
    }
}
