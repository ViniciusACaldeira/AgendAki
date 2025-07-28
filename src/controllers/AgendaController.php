<?php
namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\helpers\Flash;
use Vennizlab\Agendaki\models\AgendaModel;

class AgendaController extends Controller{
    public function index( )
    {
        $this->view("agenda/index");
    }

    public function cadastro( )
    {
        $this->view("agenda/cadastro");
    }

    public function cadastrar( )
    {
        $agenda = new AgendaModel( );
        $retorno = $agenda->cadastrar();

        if( isset($retorno['erro']) )
        {
            Flash::set("erro", $retorno['erro']);
            $this->redirect("agenda/cadastro");
        }
        else
        {
            Flash::set("sucesso", $retorno['sucesso']);
            $this->redirect("agenda");
        }
    }

    public function listar( )
    {
        $agenda = new AgendaModel( );
        $retorno = $agenda->getAgenda( );
        
        return $this->view("agenda/index", ["agendas" => $retorno]);
    }
}