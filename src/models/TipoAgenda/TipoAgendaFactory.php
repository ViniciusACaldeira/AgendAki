<?php

namespace Vennizlab\Agendaki\models\TipoAgenda;

use Vennizlab\Agendaki\helpers\TipoAgenda;
use Vennizlab\Agendaki\models\TipoAgenda\TipoAgendaLivre;
use Vennizlab\Agendaki\models\TipoAgenda\TipoAgendaSlot;

class TipoAgendaFactory{
    private static array $map = [
        TipoAgenda::LIVRE => TipoAgendaLivre::class,
        TipoAgenda::SLOT => TipoAgendaSlot::class,
    ];

    public static function build( int $tipoAgenda ) : TipoAgenda
    {
        $classe = self::$map[$tipoAgenda] ?? TipoAgendaLivre::class;
        return new $classe( );
    }

}
