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
            $id = $_GET['id'] ?? null;

            if( !isset($id) )
                return $this->response( 200, ' - Campo ID obrigatório.');

            $agendamentoModel = new AgendamentoModel( );
            $agenda_servico = $agendamentoModel->getByID( $id );

            $agendamentos = $agendamentoModel->getByAgenda( $agenda_servico['agenda_id'] );

            $inicio = $agenda_servico['inicio'];
            $fim = $agenda_servico['fim'];
            $duracao = $agenda_servico['duracao'];

            $inicio = new DateTime($agenda_servico['inicio']);   // "13:00"
            $fim = new DateTime($agenda_servico['fim']);         // "19:00"
            $duracao = $agenda_servico['duracao'];               // "00:50:00"

            $duracaoInterval = new DateInterval('PT' . (new DateTime($duracao))->format('H') . 'H' . (new DateTime($duracao))->format('i') . 'M');
            $duracaoSegundos = ($duracaoInterval->h * 3600) + ($duracaoInterval->i * 60) + $duracaoInterval->s;

            $intervalosDisponiveis = [];

            // Ordena agendamentos pelo horário de início
            usort($agendamentos, function ($a, $b) {
                return strtotime($a['inicio']) - strtotime($b['inicio']);
            });

            $ultimoFim = clone $inicio;

            foreach ($agendamentos as $agendamento) {
                $inicioAgendamento = new DateTime($agendamento['inicio']);
                $fimAgendamento = new DateTime($agendamento['fim']);

                // Calcula o horário limite para início do serviço antes do próximo agendamento
                $limiteInicio = (clone $inicioAgendamento)->sub($duracaoInterval);

                // Se houver espaço entre o último fim e o limite para começar antes do próximo agendamento
                if ($limiteInicio > $ultimoFim) {
                    $intervaloSegundos = $limiteInicio->getTimestamp() - $ultimoFim->getTimestamp();

                    if ($intervaloSegundos >= $duracaoSegundos) {
                        $intervalosDisponiveis[] = [
                            'inicio' => $ultimoFim->format('H:i'),
                            'fim'    => $limiteInicio->format('H:i'),
                        ];
                    }
                }

                // Atualiza o último fim
                if ($fimAgendamento > $ultimoFim) {
                    $ultimoFim = $fimAgendamento;
                }
            }

            // Último intervalo após o último agendamento até o fim do expediente
            if ($ultimoFim < $fim) {
                $intervaloFinalSegundos = $fim->getTimestamp() - $ultimoFim->getTimestamp();

                if ($intervaloFinalSegundos >= $duracaoSegundos) {
                    $intervalosDisponiveis[] = [
                        'inicio' => $ultimoFim->format('H:i'),
                        'fim'    => $fim->format('H:i'),
                    ];
                }
            }

            return $this->response(200, $intervalosDisponiveis);
        }
        else
            return $this->response( 400, 'Método não encontrado.');
    }


}