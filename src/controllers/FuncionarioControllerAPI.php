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
            $filtro->add( "servico" );

            $funcionarioModel = new FuncionarioModel( );
            $paginacao = $this->getPaginacao( );
            return $this->responseRetorno( $funcionarioModel->listar( $filtro, $paginacao ) );
        }
        else
            return $this->response( 400, "Não encontrado.");
    }

    public function cadastrar( )
    {
        if( $this->isPOST( ) )
        {
            $nome = $this->getCampo( "nome" );
            $telefone = $this->getCampo( "telefone" );
            $email = $this->getCampo( "email" );
            $senha = $this->getCampo( "senha" );
            $senha_confirmar = $this->getCampo( "senha_confirmar" );

            $funcionarioModel = new FuncionarioModel( );
            $retorno = $funcionarioModel->cadastrarV1( $nome, $telefone, $email, $senha, $senha_confirmar );

            return $this->responseRetorno( $retorno );
        }
        else
            return $this->responseNaoEncontrado( );
    }
}