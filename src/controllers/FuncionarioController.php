<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\helpers\Flash;
use Vennizlab\Agendaki\models\FuncionarioModel;
use Vennizlab\Agendaki\models\ServicoModel;

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

    function detalhe( )
    {
        return $this->view("funcionario/detalhe");
    }

    function atualizaServico( )
    {
        $servicoModel = new ServicoModel( );

        $servicos = $_POST['servicos'] ?? [];
        $funcionario = $_POST['funcionario_id'];
        $duracao = $_POST['servicos_duracao'];

        if( $servicoModel->atualizaServicoFuncionario( $funcionario, $servicos, $duracao ) )
            Flash::set("sucesso", "Serviços atualizados com sucesso.");
        else
            Flash::set("erro", "Falha ao atualizar os serviços.");

        return $this->redirect("funcionario/detalhe?id=" . $funcionario);
    }
}