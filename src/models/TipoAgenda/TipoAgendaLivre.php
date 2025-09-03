<?php

namespace Vennizlab\Agendaki\models\TipoAgenda;

use DateInterval;
use DateTime;
use Vennizlab\Agendaki\helpers\TipoAgenda;

class TipoAgendaLivre extends TipoAgenda{
    public function coletaHorarioDisponivel( )
    {
        $inicio = new DateTime($this->agenda_servico['inicio']);
        $fim = new DateTime($this->agenda_servico['fim']);
        $duracao = $this->agenda_servico['duracao'] ?? "00:00:00";

        $duracaoInterval = new DateInterval('PT' . (new DateTime($duracao))->format('H') . 'H' . (new DateTime($duracao))->format('i') . 'M');
        $duracaoSegundos = ($duracaoInterval->h * 3600) + ($duracaoInterval->i * 60) + $duracaoInterval->s;

        $intervalosDisponiveis = [];

        usort($this->agendamentos, function ($a, $b) {
            return strtotime($a['inicio']) - strtotime($b['inicio']);
        });

        $ultimoFim = clone $inicio;

        foreach ($this->agendamentos as $agendamento) {
            $inicioAgendamento = new DateTime($agendamento['inicio']);
            $fimAgendamento = new DateTime($agendamento['fim']);

            $limiteInicio = (clone $inicioAgendamento)->sub($duracaoInterval);

            if ($limiteInicio > $ultimoFim) {
                $intervaloSegundos = $limiteInicio->getTimestamp() - $ultimoFim->getTimestamp();

                if ($intervaloSegundos >= $duracaoSegundos) {
                    $intervalosDisponiveis[] = [
                        'inicio' => $ultimoFim->format('H:i'),
                        'fim'    => $limiteInicio->format('H:i'),
                    ];
                }
            }

            if ($fimAgendamento > $ultimoFim) {
                $ultimoFim = $fimAgendamento;
            }
        }

        if ($ultimoFim < $fim) {
            $intervaloFinalSegundos = $fim->getTimestamp() - $ultimoFim->getTimestamp();

            if ($intervaloFinalSegundos >= $duracaoSegundos) {
                $intervalosDisponiveis[] = [
                    'inicio' => $ultimoFim->format('H:i'),
                    'fim'    => $fim->format('H:i'),
                ];
            }
        }

        return $intervalosDisponiveis;
    }

    public function valida( $inicio, $horarios )
    {
        $horarioValido = false;
        $inicioTime = new DateTime( $inicio );

        foreach( $horarios as $horario)
        {
            $inicioIntervalo = new DateTime( $horario['inicio'] );
            $fimIntervalo = new DateTime( $horario['fim'] );

            if( $inicioTime >= $inicioIntervalo && $inicioTime <= $fimIntervalo )
            {
                $horarioValido = true;
                break;
            }
        }

        return $horarioValido;
    }
}