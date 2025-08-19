<?php

namespace Vennizlab\Agendaki\middlewares;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Vennizlab\Agendaki\core\Auth;
use Vennizlab\Agendaki\core\Middleware;

class AuthMiddleware implements Middleware{
    private $secret_key;
    private $tipo;

    public function __construct( $tipo = "api" )
    {
        $this->tipo = $tipo;
        $config = require(__DIR__.'/../../config/config.php');
        $this->secret_key = $config['secret_key'];
    }

    public function handle( )
    {
        if(empty($_COOKIE['token']))
        {
            if( $this->tipo == "api")
            {
                http_response_code(401);
                echo json_encode(["mensagem" => "Não autenticado."]);
            }
            else
                header('Location: /auth/login');

            exit;
        }

        try
        {
            $decoded = JWT::decode($_COOKIE['token'], new Key($this->secret_key, 'HS256'));

            Auth::setUsuario( $decoded->data );
            return $decoded->data;
        }
        catch( Exception $e )
        {
            if( $this->tipo == "api" )
            {
                http_response_code( 401 );
                echo json_encode( ["mensagem" => "Token inválido", "erro" => $e->getMessage( ) ]);
            }
            else
                header('Location: /auth/login');
            
            exit;
        }
    }
}