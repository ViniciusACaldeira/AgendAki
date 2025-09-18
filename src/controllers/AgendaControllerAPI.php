<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Auth;
use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\helpers\FiltroHelper;
use Vennizlab\Agendaki\helpers\TipoAgenda;
use Vennizlab\Agendaki\models\AgendaModel;

class AgendaControllerAPI extends Controller{
    public function getServicos( )
    {
        if( $_SERVER['REQUEST_METHOD'] == "GET" )
        {
            $agenda_id = $_GET['id'] ?? null;

            $erros = "";

            if( !$agenda_id )
                $erros .= "- Parametro id não encontrado.\n";

            if( empty($erros) )
            {
                $agendaModel = new AgendaModel( );
                $servicos = $agendaModel->getServicos( $agenda_id );
                
                return $this->response( 200, $servicos );
            }
            else
                return $this->response( 200, $erros );

        }
        else
            return $this->response( 400, "Método não encontrado.");
    }

    public function listar( )
    {
        if( $this->isGET( ) )
        {
            $paginacao = $this->getPaginacao( );

            $filtro = new FiltroHelper( $this );
            $filtro->add( "data" );
            $filtro->add( "funcionario" );
            $filtro->add( "servico" );
            
            $agendaModel = new AgendaModel( );

            return $this->responseRetorno( $agendaModel->listar( $filtro, $paginacao ) );
        }
        else
            return $this->response( 400, "Não encontrado.");
    }

    public function cadastrar( )
    {
        if( $this->isPOST( ) )
        {
            $funcionario = $this->getCampo( "funcionario_id" );
            $data = $this->getCampo( "data", [] );
            $inicio = $this->getCampo( "inicio" );
            $fim = $this->getCampo( "fim" );
            $servicos = $this->getCampo( "servicos", [] );
            $servico_inicio = $this->getCampo( "servico_inicio", [] );
            $servico_fim = $this->getCampo( "servico_fim", [] );
            $tipo = $this->getCampo( "tipo", TipoAgenda::LIVRE );
            $tamanho = $this->getCampo( "tamanho", "" );
            $quantidade = $this->getCampo( "quantidade_fila" );

            $agendaModel = new AgendaModel( );
            $retorno = $agendaModel->cadastrarV1($funcionario, $data, $inicio, $fim, $servicos, $servico_inicio, $servico_fim, $tipo, $tamanho, $quantidade );

            $this->responseRetorno( $retorno );
        }
        else
            return $this->responseNaoEncontrado( );
    }

    public function listarTipos( )
    {
        if( $this->isGET( ) )
        {
            $paginacao = $this->getPaginacao( );

            $filtro = new FiltroHelper( $this );
            
            $filtro->add( "id" );
            $filtro->add( "nome" );
            
            $agendaModel = new AgendaModel( );

            return $this->responseRetorno( $agendaModel->getTipoAgenda( $filtro, $paginacao ) );
        }
        else
            return $this->responseNaoEncontrado( );
    }
}