<?php

namespace Vennizlab\Agendaki\models;

use PDO;
use Vennizlab\Agendaki\core\Model;
use Vennizlab\Agendaki\core\Retorno;
use Vennizlab\Agendaki\helpers\DatabaseHelper;
use Vennizlab\Agendaki\helpers\FiltroHelper;
use Vennizlab\Agendaki\helpers\Paginacao;

class FuncionarioModel extends Model{

    public function cadastrar( )
    {
        $usuario = new UsuarioModel( );
        $retorno = $usuario->cadastrar( );
        
        if( $retorno['sucesso'] )
        {
            $stmt = $this->db->prepare("INSERT INTO funcionario (usuario_id) VALUES (?)");

            $retorno = $stmt->execute( [$retorno['id']] );

            if( $retorno )
                return ['sucesso' => 'Funcionário cadastrado com sucesso.'];
            else
                return ['erro' => 'Falha ao cadastrar um funcionário.'];
        }

        return $retorno;
    }

    public function getAll( )
    {
        $stmt = $this->db->prepare("SELECT f.id, u.nome, u.telefone, u.email 
                                    FROM funcionario f 
                                    INNER JOIN usuario u ON u.id = f.usuario_id");
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $usuarios;
    }

    public function getById( $id )
    {
        $stmt = $this->db->prepare( "SELECT f.id, u.nome, u.telefone, u.email 
                                     FROM funcionario f 
                                     INNER JOIN usuario u ON u.id = f.usuario_id
                                     WHERE f.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function listar( FiltroHelper $filtro, ?Paginacao $paginacao = null )
    {
        $query = new DatabaseHelper( );
        $query->setSQL( "SELECT f.id, u.nome, u.telefone, u.email 
                         FROM funcionario f 
                         INNER JOIN usuario u ON u.id = f.usuario_id" );

        $query->setPaginacao( $paginacao );

        if( $filtro->tem( "id" ) )
        {
            $id = $filtro->get( "id" );
            if( !empty( $id ) )
                $query->addCondicao( "f.id = ?", $id );
        }

        if( $filtro->tem( "nome" ) )
        {
            $nome = $filtro->get( "nome" );
            
            if( !empty( $nome ) )
                $query->addCondicao( "UPPER(u.nome) LIKE UPPER(?)", "%$nome%" );
        }

        if( $filtro->tem( "telefone" ) )
        {
            $telefone = $filtro->get( "telefone" );
            
            if( !empty( $telefone ) )
                $query->addCondicao( "u.telefone LIKE ? ", "%$telefone%" );
        }

        $stmt = $query->execute( $this->db );

        return new Retorno( Retorno::SUCESSO, $stmt->fetchAll(PDO::FETCH_ASSOC) );
    }
}