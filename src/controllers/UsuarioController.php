<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\controller;
use Vennizlab\Agendaki\helpers\Flash;
use Vennizlab\Agendaki\models\UsuarioModel;

class UsuarioController extends Controller
{
    public function login( )
    {
        if( $_SERVER['REQUEST_METHOD'] == "POST" )
        {
            $usuario = new UsuarioModel( );

            $retorno = $usuario->login( );

            if( isset($retorno['erro'] ) )
                Flash::set("erro", $retorno['erro']);
            else
                Flash::set("sucesso", $retorno["sucesso"]);
            
            if( isset($retorno['sucesso']) )
                $this->redirect('dashboard');
            else
                $this->redirect("auth/login", $retorno);
        }
        else
            $this->view("login");
    }

    public function cadastrar( )
    {
        $usuario = new UsuarioModel( );

        $retorno = $usuario->cadastrar( );

        if( isset($retorno['erro'] ) )
            Flash::set("erro", $retorno['erro']);
        else
            Flash::set("sucesso", $retorno["sucesso"]);

        $this->redirect("auth/login", $retorno);
    }

    public function logout( )
    {
        $usuario = new UsuarioModel( );
        $retorno = $usuario->logout( );

        if( isset($retorno['erro'] ) )
            Flash::set("erro", $retorno['erro']);
        else
            Flash::set("sucesso", $retorno["sucesso"]);
        
        $this->redirect("auth/login", $retorno);
    }
}