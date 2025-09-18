<?php
namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Auth;
use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\helpers\FiltroHelper;
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
        if( $this->isGET( ) )
        {
            $paginacao = $this->getPaginacao( );
            $filtro = new FiltroHelper( $this );

            if( Auth::isFuncionario( ) )
            {
                $filtro->add( "data" );
                $filtro->add( "funcionarios" );
                $filtro->add( "servicos" );
                $filtro->add( "clientes" );
            }
            else
            {
                $filtro->addFiltro( "clientes", Auth::usuario( )->id );
            }

            $filtro->add( "apartir" );
            
            if( !Auth::isFuncionario( ) )
                $filtro->addFiltro( "ORDERBY", "ASC" );

            $agendamentoModel = new AgendamentoModel( );
            
            $retorno = $agendamentoModel->getAgendamentosV1( $filtro, $paginacao );

            return $this->responseRetorno( $retorno );
        }
        else
            return $this->responseNaoEncontrado( );
    }

    public function cadastrar( )
    {
        if( $this->isPOST( ) )
        {
            $agenda_servico = $this->getCampo( "agenda_servico" );
            if( Auth::isFuncionario( ) )
                $usuario_id = $this->getCampo( "usuario_id", Auth::usuario( )->id );
            else
                $usuario_id = Auth::usuario( )->id;

            $inicio = $this->getCampo( "inicio" );

            $agendamentoModel = new AgendamentoModel( );
            $retorno = $agendamentoModel->cadastrarV1( $agenda_servico, $usuario_id, $inicio );

            return $this->responseRetorno( $retorno );
        }
        else
            return $this->response( 400, 'Método não encontrado.');
    }
}