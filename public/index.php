<?php
require_once __DIR__ . '/../vendor/autoload.php';

if( session_status() != PHP_SESSION_ACTIVE)
    session_start();

// Carrega rotas
$routes = require __DIR__ . '/../routes/web.php';

// Obtém URL da requisição (padrão: users/index)
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? 'auth/login';
$url = rtrim($url, '/');

if (array_key_exists($url, $routes)) {
    [$controllerClass, $method, $middlewares] = array_pad($routes[$url], 3, []);

    if( !empty($middlewares) )
    {
        foreach( $middlewares as $middlewareDef ) 
        {
            if( is_array( $middlewareDef ) )
            {
                $middlewareClass = array_shift($middlewareDef);
                $middleware = new $middlewareClass(...$middlewareDef);
            }
            else
                $middleware = new $middlewareDef( );

            $middleware->handle( );
        }
    } 

    $controller = new $controllerClass();
    if (method_exists($controller, $method)) {
        $controller->$method();
    } else {
        http_response_code(404);
        echo "Método {$method} não encontrado.";
    }
} 
else 
{
    http_response_code(404);
    echo "Rota '{$url}' não registrada.";
}