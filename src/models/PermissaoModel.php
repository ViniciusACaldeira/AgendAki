<?php

namespace Vennizlab\Agendaki\models;

use Exception;
use PDO;
use Vennizlab\Agendaki\core\Model;
use Vennizlab\Agendaki\core\Retorno;
use Vennizlab\Agendaki\helpers\DatabaseHelper;
use Vennizlab\Agendaki\helpers\ValidacaoHelper;

class PermissaoModel extends Model{
    public function listar( $id )
    {
        $query = new DatabaseHelper( );
        $query->setSQL( "SELECT * FROM permissao p" );
        
        if( isset( $id ) )
            $query->addCondicao( "p.id = ?", $id );

        $retorno = $query->execute( $this->db );

        if( $retorno )
            return new Retorno( Retorno::SUCESSO, $retorno->fetchAll( PDO::FETCH_ASSOC ) );
        else
            return new Retorno( Retorno::ERRO, "Falha ao consultar as permissões.");
    }

    public function vincularFuncionario( $funcionario, $permissoes )
    {
        $validacao = new ValidacaoHelper( );

        if( !$validacao->vazio( "É obrigatório o funcionario.", $funcionario ) )
        {
            $funcionarioModel = new FuncionarioModel( );
            $validacao->vazio( "Funcionário não encontrado.", $funcionarioModel->getById( $funcionario ) );
        }

        if( $validacao->temErro( ) )
            return new Retorno( Retorno::ERRO_VALIDACAO, $validacao->getValidacao( ) );

        try
        {
            $stmt = $this->db->prepare( "DELETE FROM funcionario_permissao WHERE funcionario_id = ?" );
            $retorno = $stmt->execute( [$funcionario] );

            if( $retorno && !empty( $permissoes ) )
            {
                $parametros = "";
                $parametrosValores = [];

                $this->getParametros( $permissoes );

                foreach( $permissoes as $permissao )
                {
                    $parametrosValores[] = $funcionario;
                    $parametrosValores[] = $permissao;
                    $parametros .= "(?,?),";
                }

                $parametros = rtrim( $parametros, "," );

                $stmt = $this->db->prepare( "INSERT INTO funcionario_permissao (funcionario_id, permissao_id) VALUES $parametros");
                $retorno = $stmt->execute( $parametrosValores );

                if( !$retorno )
                    return new Retorno( Retorno::ERRO, "Falha ao víncular as permissões ao funcionário." );
            }

            return new Retorno( Retorno::SUCESSO, "Permissão vínculada com sucesso." );
        }
        catch( Exception $e )
        {
            return new Retorno( Retorno::ERRO, "Falha ao víncular as permissões: " . $e->getMessage( ) );
        }
    }

    public function getPermissaoFuncionario( $funcionario )
    {
        $validacao = new ValidacaoHelper( );
        $validacao->vazio( "É obrigatório o funcionario.", $funcionario );

        if( $validacao->temErro( ) )
            return new Retorno( Retorno::ERRO_VALIDACAO, $validacao->getValidacao( ) );

        $query = new DatabaseHelper( );
        $query->setSQL( "SELECT p.*
                         FROM permissao p
                         INNER JOIN funcionario_permissao fp ON p.id = fp.permissao_id" );

        $query->addCondicao( "fp.funcionario_id = ?", $funcionario );

        $retorno = $query->execute( $this->db );

        if( $retorno )
            return new Retorno( Retorno::SUCESSO, $retorno->fetchAll( PDO::FETCH_ASSOC ) );
        else
            return new Retorno( Retorno::ERRO, "Falha ao listar permissões." );
    }
}