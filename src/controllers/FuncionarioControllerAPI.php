<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\models\FuncionarioModel;

class FuncionarioControllerAPI extends Controller{
    public function listar( )
    {
        if( $this->isGET( ) )
        {
            $id = $this->getCampo( "id" );

            $funcionarioModel = new FuncionarioModel( );
            
            return $this->responseRetorno( $funcionarioModel->listar( $id ) );
        }
        else
            return $this->response( 400, "Não encontrado.");
    }
}