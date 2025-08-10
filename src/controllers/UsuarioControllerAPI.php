<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\models\UsuarioModel;

class UsuarioControllerAPI extends Controller{

    public function listarClientes( )
    {
        if( $this->isGET( ) )
        {
            $usuarioModel = new UsuarioModel( );
            return $this->responseRetorno( $usuarioModel->listarClientes( ) );
        }
        else
            return $this->response( 400, "Não encontrado." );
    }
}