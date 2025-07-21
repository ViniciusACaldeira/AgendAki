<?php

namespace Vennizlab\Agendaki\models;

use PDO;
use Vennizlab\Agendaki\core\Model;

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
        $stmt = $this->db->prepare("SELECT * FROM funcionario f 
                                    INNER JOIN usuario u ON u.id = f.usuario_id");
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $usuarios;
    }
}