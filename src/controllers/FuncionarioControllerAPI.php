<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\helpers\FiltroHelper;
use Vennizlab\Agendaki\models\FuncionarioModel;

class FuncionarioControllerAPI extends Controller{
    public function listar( )
    {
        if( $this->isGET( ) )
        {
            $filtro = new FiltroHelper( $this );
            $filtro->add( "id" );
            $filtro->add( "nome" );
            $filtro->add( "email" );
            $filtro->add( "telefone" );

            $funcionarioModel = new FuncionarioModel( );
            $paginacao = $this->getPaginacao( );
            return $this->responseRetorno( $funcionarioModel->listar( $filtro, $paginacao ) );
        }
        else
            return $this->response( 400, "Não encontrado.");
    }
}