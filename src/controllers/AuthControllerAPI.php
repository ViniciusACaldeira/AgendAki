<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\models\AuthModel;
use Vennizlab\Agendaki\models\UsuarioModel;

class AuthControllerAPI extends Controller{

    public function login( )
    {
        if( $this->isPOST( ) )
        {
            $login = $this->getCampo( "login" );
            $senha = $this->getCampo( "senha" );

            $authModel = new AuthModel( );
            return $this->responseRetorno( $authModel->login( $login, $senha ) );
        }
        else
            return $this->responseNaoEncontrado( );
    }

    public function logout( )
    {

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

            $usuarioModel = new UsuarioModel( );
            $retorno = $usuarioModel->cadastrarV1( $nome, $telefone, $email, $senha, $senha_confirmar );
            return $this->responseRetorno( $retorno );
        }
        else
            return $this->responseNaoEncontrado( );
    }
}