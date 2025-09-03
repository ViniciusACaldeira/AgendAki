<?php

namespace Vennizlab\Agendaki\models\TipoAgenda;

use DateInterval;
use DateTime;
use Vennizlab\Agendaki\helpers\TipoAgenda;

class TipoAgendaSlot extends TipoAgenda{
    public function coletaHorarioDisponivel( )
    {
        $tamanho = $this->agenda['tamanho'];
        $quantidade_fila = $this->agenda['quantidade_fila'];
        $inicio = $this->agenda['inicio'];
        $fim = $this->agenda['fim'];

        $slots = [];

        if( $tamanho == '00:00:00' )
            return $slots;

        $dataTamanho = new DateTime( $tamanho );
        $tamanho = $dataTamanho->format("H:i");

        $dataTamanho = new DateInterval('PT' . $dataTamanho->format('H') . 'H' . $dataTamanho->format('i') . 'M');

        $dataInicio = new DateTime( $inicio );
        $dataFim = new DateTime( $fim );

        while( $dataInicio < $dataFim )
        {
            $slots[] = [ "hora" => clone $dataInicio, "disponivel" => true, "quantidade_fila" => $quantidade_fila];
            $dataInicio->add( $dataTamanho );
        }

        $agendamentos = $this->agendamentos;
        $duracao = $this->agenda_servico['duracao'];
        $servico_inicio = $this->agenda_servico['inicio'];
        $servico_fim = $this->agenda_servico['fim'];

        $data_servico_inicio = new DateTime( $servico_inicio );
        $data_servico_fim = new DateTime( $servico_fim );

        $slotsDisponiveis = [];
        foreach( $slots as $slot )
        {
            if( $slot['hora'] < $data_servico_inicio || $slot['hora'] > $data_servico_fim )
                continue;
            
            $slot['hora'] = $slot['hora']->format( "H:i" );

            $slotsDisponiveis[] = $slot;
        }

        foreach( $agendamentos as $agendamento )
        {
            $agendamento_inicio = $agendamento['inicio'];
            $agendamento_fim    = $agendamento['fim'];

            $data_agendamento_inicio = new DateTime( $agendamento_inicio );
            $data_agendamento_fim = new DateTime( $agendamento_fim );
            $agendamento_duracao = $data_agendamento_inicio->diff( $data_agendamento_fim );

            $quantidade_slots_ocupados = $this->calcularSlotsNecessarios( sprintf("%02d:%02d", $agendamento_duracao->h, $agendamento_duracao->i), $tamanho );

            $indicieInicio = array_search( $data_agendamento_inicio->format( "H:i" ), array_column( $slotsDisponiveis, "hora" ) );

            if( $indicieInicio !== false )
            {
                for( $i = $indicieInicio; $i < $indicieInicio+$quantidade_slots_ocupados; $i++)
                {
                    $slot =& $slotsDisponiveis[$i];

                    if( isset( $slot ) )
                    {
                        $quantidade = $slot['quantidade_fila' ] - 1;
                        
                        if( $quantidade < 0 )
                            $slot['disponivel'] = false;
                        else
                            $slot['quantidade_fila'] = $quantidade;
                    }
                }
            }
        }

        $indisponiveis = array_keys( array_filter( $slotsDisponiveis, fn($s) => !$s['disponivel'] ) );

        $slotsNecessarios = $this->calcularSlotsNecessarios( $duracao, $tamanho ); 

        for( $i = 0; $i < count($indisponiveis); $i++ ) 
        {
            $indice = $indisponiveis[$i];

            $proximoIndisponivel = ( $i + 1 < count( $indisponiveis ) ) ? $indisponiveis[$i + 1] : count( $slotsDisponiveis );

            $disponivelMaximo = $proximoIndisponivel - $slotsNecessarios;

            for ($j = $indice + 1; $j < $proximoIndisponivel; $j++)
                $slotsDisponiveis[$j]['disponivel'] = $j <= $disponivelMaximo;
        }

        return $slotsDisponiveis;
    }

    function duracaoParaMinutos( string $duracao ) 
    {
        [$h, $m] = explode(':', $duracao);
        return ((int)$h * 60) + (int)$m;
    }

    function calcularSlotsNecessarios( string $duracaoServico, string $tamanhoSlot ) 
    {
        $minutosDuracao = $this->duracaoParaMinutos($duracaoServico);
        $minutosSlot = $this->duracaoParaMinutos($tamanhoSlot);

        $numSlots = ceil($minutosDuracao / $minutosSlot);

        return (int)$numSlots;
    }

    public function valida( $inicio, $horarios )
    {
        return !empty( array_filter( $horarios, function($h) use ($inicio) {
            return $h['disponivel'] && $h['hora'] == $inicio;
        }));
    }
}