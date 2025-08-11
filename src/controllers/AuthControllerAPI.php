<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\models\AuthModel;

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

}