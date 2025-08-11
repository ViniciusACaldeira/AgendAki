<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\models\PermissaoModel;

class PermissaoControllerAPI extends Controller{
    public function listar( )
    {
        if( $this->isGET( ) )
        {
            $id = $this->getCampo( "id" );

            $permissaoModel = new PermissaoModel( );
            $retorno = $permissaoModel->listar( $id );
            
            return $this->responseRetorno( $retorno ); 
        }
        else
            return $this->responseNaoEncontrado( );
    }
    
    public function vincular( )
    {
        if( $this->isPOST() )
        {
            $funcionario = $this->getCampo( "funcionario" );
            $permissoes = $this->getCampo( "permissoes" );

            $permissaoModel = new PermissaoModel( );
            
            return $this->responseRetorno( $permissaoModel->vincularFuncionario( $funcionario, $permissoes ) );
        }
        else
            return $this->responseNaoEncontrado( );
    }

    public function listarFuncionario( )
    {
        if( $this->isGET( ) )
        {
            $id = $this->getCampo( "id" );
            
            $permissaoModel = new PermissaoModel( );
            return $this->responseRetorno( $permissaoModel->getPermissaoFuncionario( $id ) );
        }
        else
            return $this->responseNaoEncontrado( );
    }
}