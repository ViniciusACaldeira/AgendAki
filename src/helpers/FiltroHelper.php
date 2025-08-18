<?php

namespace Vennizlab\Agendaki\helpers;

class FiltroHelper
{
    private array $filtro = [];
    private $controller;

    public function __construct( $controller ) {
        $this->controller = $controller;
    }

    public function addFiltro( string $campo, $valor )
    {
        $this->filtro[] = [ "campo" => $campo,
                            "valor"=> $valor ];
    }

    public function getFiltros( ) : array
    {
        return $this->filtro;
    }

    public function add( string $campo, bool $obrigatorio = false, $default = null ) // O Filtro é obrigatório.
    {
        if( !$obrigatorio && !$this->controller->temCampo( $campo ) )
            return;

        $this->addFiltro( $campo, $this->controller->getCampo( $campo, $default ) );
    }

    public function tem( string $campo ): bool
    {
        foreach( $this->filtro as $f )
            if( $f['campo'] === $campo )
                return true;

        return false;
    }

    public function get( $campo )
    {
        foreach( $this->filtro as $f )
            if( $f['campo'] === $campo )
                return $f['valor'];

        return null;
    }
}