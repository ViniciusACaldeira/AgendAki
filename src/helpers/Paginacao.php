<?php

namespace Vennizlab\Agendaki\helpers;

class Paginacao{
    private int $pagina;
    private int $registroPorPagina;
    private int $total;
    private $paginado = false;
    
    public function init( )
    {
        $this->pagina = (int)($_GET['page'] ?? -1);
        $this->registroPorPagina = (int)($_GET['per_page'] ?? 10);

        if( $this->pagina > 0 )
        {
            $this->paginado = true;

            if( $this->pagina > 100 )
                $this->pagina = 100;
        }
    }

    public function ePaginado( )
    {
        return $this->paginado;
    }

    public function getRegistroPorPagina( ) :int
    {
        return $this->registroPorPagina;
    }

    public function getOFFSET( ) :int
    {
        return ($this->pagina-1) * $this->getRegistroPorPagina( );
    }

    public function setTotal( int $total )
    {
        $this->total = $total;
    }

    public function response( )
    {
        return ["page" => $this->pagina, "per_page" => $this->registroPorPagina, "total" => $this->total, "paginas" => ceil( $this->total/$this->registroPorPagina) ];
    }
}