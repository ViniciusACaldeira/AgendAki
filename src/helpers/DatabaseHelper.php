<?php

namespace Vennizlab\Agendaki\helpers;

class DatabaseHelper{

    private string $sql;
    private array $parametros = [];
    private array $where = [];

    public function setSQL( $sql )
    {
        $this->sql = $sql;
    }

    public function addParametro( $parametro )
    {
        if( is_array($parametro) )
            foreach ($parametro as $p) 
                $this->parametros[] = $p;
        else
            $this->parametros[] = $parametro;
    }

    public function addWhere( $where )
    {
        $this->where[] = $where;
    }

    public function addCondicao( $where, $parametro )
    {
        $this->addWhere( $where );
        $this->addParametro( $parametro );
    }

    public function getParametros( )
    {
        return $this->parametros;
    }
    
    public function getSQL( )
    {
        $query = $this->sql;
        $where = $this->where;

        for( $i = 0; $i < count($where); $i++ )
        {
            $query .= $i == 0 ? " WHERE " : " AND ";
            $query .= $where[$i] . " ";
        }

        return $query;
    }

    public function execute( $db )
    {
        $stmt = $db->prepare( $this->getSQL( ) );
        $stmt->execute( $this->getParametros( ) );

        return $stmt;
    }
}