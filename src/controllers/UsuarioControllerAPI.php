<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Auth;
use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\helpers\FiltroHelper;
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
            return $this->responseNaoEncontrado( );
    }

    public function alterar( )
    {
        if( $this->isPOST( ) )
        {
            if( Auth::isFuncionario( ) )
            {
                $usuario = $this->getCampo( "usuario", Auth::usuario( )->id);
            }
            else
                $usuario = Auth::usuario( )->id;

            $filtro = new FiltroHelper( $this );
            $filtro->add( "nome" );
            $filtro->add( "email" );
            $filtro->add( "telefone" );

            $usuarioModel = new UsuarioModel( );
            
            return $this->responseRetorno( $usuarioModel->alterar( $usuario, $filtro ) );
        }
        else
            return $this->responseNaoEncontrado( );
    }

    public function usuario( )
    {
        if( $this->isGET( ) )
        {
            $usuario = Auth::usuario( )->id;
            $usuarioModel = new UsuarioModel( );

            return $this->responseRetorno( $usuarioModel->get( $usuario ) );
        }
        else
            return $this->responseNaoEncontrado( );
    }
}