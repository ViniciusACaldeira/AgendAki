<?php

namespace Vennizlab\Agendaki\middlewares;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Vennizlab\Agendaki\core\Auth;
use Vennizlab\Agendaki\core\Middleware;

class AuthMiddleware implements Middleware{
    
    private $secret_key;

    public function __construct( )
    {
        $config = require(__DIR__.'/../../config/config.php');
        $this->secret_key = $config['secret_key'];
    }

    public function handle( )
    {
        $headers = getallheaders();

        if( !isset($headers['Authorization']))
        {
            http_response_code(401);
            echo json_encode(["mensagem" => "Token não enviado."]);
            exit;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);

        try
        {
            $decoded = JWT::decode($token, new Key($this->secret_key, 'HS256'));

            Auth::setUsuario( $decoded->data );
            return $decoded->data;
        }
        catch( Exception $e )
        {
            http_response_code( 401 );
            echo json_encode( ["mensagem" => "Token inválido", "erro" => $e->getMessage( ) ]);
            exit;
        }
    }
}