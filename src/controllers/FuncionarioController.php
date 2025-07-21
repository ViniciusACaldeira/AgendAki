<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\helpers\Flash;
use Vennizlab\Agendaki\models\FuncionarioModel;

class FuncionarioController extends Controller{

    function index( )
    {
        return $this->view("funcionario");
    }

    function cadastrar( )
    {
        $funcionario = new FuncionarioModel( );
        $retorno = $funcionario->cadastrar( );

        if( isset($retorno['erro'] ) )
            Flash::set("erro", $retorno['erro']);
        else
            Flash::set("sucesso", $retorno["sucesso"]);

        return $this->redirect("funcionario");
    }

    function cadastro( )
    {
        return $this->view("funcionario/cadastro");
    }
}