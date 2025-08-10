<?php
namespace Vennizlab\Agendaki\controllers;

use DateInterval;
use DateTime;
use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\models\AgendamentoModel;

class AgendamentoControllerAPI extends Controller
{
    public function servicosDisponiveis( )
    {
        if( $_SERVER['REQUEST_METHOD'] == "GET")
        {
            $id = $this->getCampo( "id" );

            $agendamentoModel = new AgendamentoModel( );
            $retorno = $agendamentoModel->getHorariosDisponiveisServicoAgenda( $id );

            return $this->responseRetorno( $retorno );
        }
        else
            return $this->response( 400, 'Método não encontrado.');
    }

    public function listar( )
    {
        if( $_SERVER['REQUEST_METHOD'] == "POST" )
        {
            $data = $_POST['data'] ?? null;
            $funcionarios = $_POST['funcionarios_id'] ?? null;
            $servicos = $_POST['servicos_id'] ?? null;
            $usuarios = $_POST['usuarios_id'] ?? null;

            if( !$data && !$funcionarios && !$servicos && !$usuarios )
                return $this->response( 200, ' - Pelo menos um parâmetro obrigatório.' );

            $agendamentoModel = new AgendamentoModel( );
            $agendamentos = $agendamentoModel->getAgendamentos( $data, $funcionarios, $servicos, $usuarios );
            return $this->response( 200, $agendamentos);
        }
        else
            return $this->response( 400, 'Método não encontrado.');
    }

    public function cadastrar( )
    {
        if( $this->isPOST( ) )
        {
            $agenda_servico = $this->getCampo( "agenda_servico" );
            $usuario_id = $this->getCampo( "usuario_id" );
            $inicio = $this->getCampo( "inicio" );

            $agendamentoModel = new AgendamentoModel( );
            $retorno = $agendamentoModel->cadastrarV1( $agenda_servico, $usuario_id, $inicio );

            return $this->responseRetorno( $retorno );
        }
        else
            return $this->response( 400, 'Método não encontrado.');
    }
}