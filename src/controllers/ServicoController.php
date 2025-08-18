<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\helpers\Flash;
use Vennizlab\Agendaki\models\ServicoModel;

class ServicoController extends Controller{

    public function index( )
    {
        $this->view("servico/index");
    }

    public function cadastro( )
    {
        $this->view("servico/cadastro");
    }

    public function cadastrar( )
    {
        $servico = new ServicoModel( );
        $retorno = $servico->cadastrar( );

         if( isset($retorno['erro'] ) )
         {
            Flash::set("erro", $retorno['erro']);
            return $this->redirect("servico/cadastro");
         }
        else
        {
            Flash::set("sucesso", $retorno["sucesso"]);
            return $this->redirect('servico');
        }
    }

    public function detalhe( )
    {
        $this->view( "servico/detalhe" );
    }
}