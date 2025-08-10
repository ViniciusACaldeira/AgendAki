<?php

namespace Vennizlab\Agendaki\models;

use PDO;
use Vennizlab\Agendaki\core\Model;
use Vennizlab\Agendaki\core\Retorno;
use Vennizlab\Agendaki\helpers\DatabaseHelper;

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
    
    public function listar( $id )
    {
        $query = new DatabaseHelper( );
        $query->setSQL( "SELECT f.id, u.nome, u.telefone, u.email 
                         FROM funcionario f 
                         INNER JOIN usuario u ON u.id = f.usuario_id" );

        if( !empty( $id ) )
            $query->addCondicao( "f.id = ?", $id );

        $stmt = $this->db->prepare( $query->getSQL( ) );
        $stmt->execute( $query->getParametros( ) );

        return new Retorno( Retorno::SUCESSO, $stmt->fetchAll(PDO::FETCH_ASSOC) );
    }
}