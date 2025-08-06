<?php

namespace Vennizlab\Agendaki\core;

class Model{
    
    protected $db;

    public function __construct( )
    {
        $this->db = require(__DIR__.'/../../config/database.php');
    }

    public function getParametros( &$valores )
    {
        if( !empty( $valores ) )
        {
            if( !is_array($valores) )
                $valores = explode(",", $valores);

            return implode(',', array_fill(0, count($valores), '?'));
        }
        else
            return "";
    }
}