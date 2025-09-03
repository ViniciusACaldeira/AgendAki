<?php

namespace Vennizlab\Agendaki\helpers;

abstract class TipoAgenda{
    const LIVRE = 1;
    const DIFERENCA_LIMITADA = 2;
    const SLOT = 3;
    const SLOT_HIBRIDO = 4;

    abstract function coletaHorarioDisponivel( );

    public $agenda;
    public $agenda_servico;
    public $agendamentos;
    
    public function setAgenda( $agenda )
    {
        $this->agenda = $agenda;
    }

    public function setAgendaServico( $agenda_servico )
    {
        $this->agenda_servico = $agenda_servico;
    }

    public function setAgendamentos( $agendamentos )
    {
        $this->agendamentos = $agendamentos;
    }

    abstract function valida( $inicio, $horarios );
}