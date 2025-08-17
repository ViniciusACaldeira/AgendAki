<?php

namespace Vennizlab\Agendaki\helpers;

class DatabaseHelper{

    private string $sql;
    private array $parametros = [];
    private array $where = [];
    private ?Paginacao $paginacao = null;

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

    public function setPaginacao( Paginacao $paginacao )
    {
        if( $paginacao == null )
            $this->paginacao = new Paginacao( );
        else
            $this->paginacao = $paginacao;
    }

    private function getPaginacao( ) : Paginacao
    {
        if( $this->paginacao == null )
            return new Paginacao( );
        else
            return $this->paginacao;
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
        if( !$this->getPaginacao( )->ePaginado( ) )
        {
            $stmt = $db->prepare( $this->getSQL( ) );
            $stmt->execute( $this->getParametros( ) );

            return $stmt;
        }
        else
        {
            $query = $this->getSQL( );
            $query = preg_replace('/SELECT\s+.*?\s+FROM/i', 'SELECT COUNT(*) FROM', $query);

            $stmt = $db->prepare( $query );
            $stmt->execute( $this->getParametros( ) );
            $total = $stmt->fetchColumn( );

            $this->getPaginacao( )->setTotal( $total );

            $query = $this->getSQL( );
            $query .= " LIMIT " . $this->getPaginacao( )->getRegistroPorPagina( ) . " ";
            $query .= " OFFSET " . $this->getPaginacao( )->getOFFSET( ) . " ";

            $stmt = $db->prepare( $query );
            $stmt->execute( $this->getParametros( ) );

            return $stmt;
        }
    }
}