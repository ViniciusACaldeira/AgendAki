<?php

namespace Vennizlab\Agendaki\middlewares;

use Vennizlab\Agendaki\core\Auth;
use Vennizlab\Agendaki\core\Middleware;

class FuncionarioMiddleware implements Middleware
{
    private $rotas_liberadas = [""];
    private $isAPI = true;
    private $url;

    public function __construct( $url, $api = 'api' )
    {
        $this->url = $url;
        $this->isAPI = $api == 'api';
    }

    public function handle( )
    {
        if( in_array( $this->url, $this->rotas_liberadas ) )
            return;

        $usuario = Auth::usuario( );

        if( $usuario->funcionario && $usuario->funcionario_id > 0 )
            return;

        http_response_code( 404 );

        if( $this->isAPI )
            echo json_encode( ["status" => 404, "mensagem" => "Método não encontrado.", "data" => [] ] );
        else
            header('Location: /notFound');        

        exit;
    }
}