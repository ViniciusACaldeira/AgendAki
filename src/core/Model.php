<?php

namespace Vennizlab\Agendaki\core;

class Model{
    
    protected $db;

    public function __construct( )
    {
        $this->db = require(__DIR__.'/../../config/database.php');
    }

}