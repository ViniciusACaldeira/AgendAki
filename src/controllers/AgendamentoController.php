<?php
namespace Vennizlab\Agendaki\controllers;

use DateInterval;
use DateTime;
use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\helpers\Flash;
use Vennizlab\Agendaki\models\AgendamentoModel;

class AgendamentoController extends Controller{
    public function index( )
    {
        $this->view("agendamento/index");
    }

    public function cadastro( )
    {
        $this->view("agendamento/cadastro");
    }

    public function cadastrar( )
    {
        $agendamentoModel = new AgendamentoModel( );

        $agenda_servico = $_POST['servico_id'] ?? null;
        $inicio = $_POST['inicio'] ?? null;
        $usuario_id = $_POST['usuario_id'] ?? null;

        $validacao = "";
        if( !$agenda_servico || !$inicio || !$usuario_id)
            $validacao .= "Todos os campos são obrigatórios\n";    

        $agenda = null;
        if( isset($agenda_servico) )
        {
            $agenda = $agendamentoModel->getByID($agenda_servico);

            if( !isset( $agenda ) )
                $validacao .= "Não foi encontrado agenda_servico.";
        }
        
        if( empty($validacao) )
        {
            $fim = new DateTime($inicio);
            $duracao = $agenda['duracao']; // Ex: "00:50:00"
            list($h, $m, $s) = explode(':', $duracao);
            $duracaoInterval = new DateInterval("PT{$h}H{$m}M");
            $fim->add($duracaoInterval);
            $fim = $fim->format("H:i");

            if( $agendamentoModel->cadastrar( $agenda_servico, $usuario_id, $inicio, $fim ) )
            {
                Flash::set("sucesso", "Agendamento realizado com sucesso.");
                return $this->redirect("agendamento/cadastro");
            }
            else
                Flash::set("erro", "Falha ao cadastrar agendamento.");
        }
        else
            Flash::set("erro", $validacao);
        

        return $this->redirect("agendamento/cadastro");
    }
}