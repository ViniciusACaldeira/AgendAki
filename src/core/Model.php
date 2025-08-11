<?php

namespace Vennizlab\Agendaki\core;

class Model{
    
    protected $db;
    protected $config;

    public function __construct( )
    {
        $this->config = require(__DIR__.'/../../config/config.php');
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