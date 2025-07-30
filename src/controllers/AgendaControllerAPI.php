<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Controller;
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
}