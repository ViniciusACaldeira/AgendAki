<?php

namespace Vennizlab\Agendaki\models;

use Exception;
use PDO;
use Vennizlab\Agendaki\core\Auth;
use Vennizlab\Agendaki\core\Model;
use Vennizlab\Agendaki\core\Retorno;
use Vennizlab\Agendaki\helpers\DatabaseHelper;
use Vennizlab\Agendaki\helpers\FiltroHelper;
use Vennizlab\Agendaki\helpers\ValidacaoHelper;

class UsuarioModel extends Model{

    public function all( )
    {
        $stmt = $this->db->query("SELECT * FROM usuario");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllCliente( )
    {
        $stmt = $this->db->query("SELECT u.id, u.nome, u.telefone, u.email 
                                    FROM usuario u
                                    LEFT JOIN funcionario f ON f.usuario_id = u.id
                                    WHERE f.id IS NULL");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function excluirUsuario( $id )
    {
        $query = new DatabaseHelper( );
        $query->setSQL( "DELETE FROM usuario" );
        $query->addCondicao( "id = ?", $id );

        try
        {
            $retorno = $query->execute( $this->db );

            if( $retorno )
                return new Retorno( Retorno::SUCESSO, "Usuário excluído com sucesso." );
            else
                return new Retorno( Retorno::ERRO, "Falha ao cadastrar usuário." );
        }
        catch( Exception $e )
        {
            return new Retorno( Retorno::ERRO, "Falha ao excluir usuário: ", $e->getMessage( ) );
        }
    }

    public function login()
    {
        $login = $_POST['login'] ?? null;
        $senha = $_POST['senha'] ?? null;

        if (!$login || !$senha)
            return ['erro' => 'Todos os campos são obrigatórios.'];

        $stmt = $this->db->prepare("SELECT id, nome, email, telefone, senha FROM usuario WHERE email = ? OR telefone = ? LIMIT 1");
        $stmt->execute([$login, $login]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario || !password_verify($senha, $usuario['senha'])) {
            return ['erro' => 'Credenciais inválidas.'];
        }

        // Inicia sessão e armazena dados do usuário
        $_SESSION['user'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'telefone' => $usuario['telefone']
        ];

        return ['sucesso' => 'Login realizado com sucesso.'];
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        return ['sucesso' => 'Logout realizado.'];
    }

    public function cadastrar( ) 
    {
        $nome = $_POST['nome'] ?? null;
        $email = $_POST['email'] ?? null;
        $telefone = $_POST['telefone'] ?? null;
        $senha = $_POST['senha'] ?? null;
        $senha_confirmar = $_POST['senha_confirmar'] ?? null;

        if (!$nome || !$email || !$telefone || !$senha || !$senha_confirmar)
            return ['erro' => 'Todos os campos são obrigatórios.'];

        if ($senha !== $senha_confirmar)
            return ['erro' => 'As senhas não coincidem.'];

        if ($this->existeUsuario($email, $telefone))
            return ['erro' => 'Usuário já cadastrado com este e-mail ou telefone.'];

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("INSERT INTO usuario (nome, email, telefone, senha) VALUES (?, ?, ?, ?)");
        $sucesso = $stmt->execute([$nome, $email, $telefone, $senhaHash]);

        if ($sucesso)
        {
            $id = $this->db->lastInsertId();
            return ['sucesso' => 'Usuário cadastrado com sucesso.', 'id' => $id];
        }
        else
            return ['erro' => 'Erro ao cadastrar usuário.'];
    }

    public function existeUsuario($email, $telefone, $id = -1) {
        $stmt = $this->db->prepare("SELECT 1 FROM usuario WHERE telefone = ? OR email = ? OR id = ? LIMIT 1");
        $stmt->execute([$telefone, $email, $id]);
        return $stmt->fetchColumn() !== false;
    }

    public function existeUsuarioByID( $id )
    {
        return $this->existeUsuario('','',$id);
    }

    public function listarClientes( )
    {
        $stmt = $this->db->query("SELECT u.id, u.nome, u.telefone, u.email 
                                  FROM usuario u
                                  LEFT JOIN funcionario f ON f.usuario_id = u.id
                                  WHERE f.id IS NULL");

        return new Retorno( Retorno::SUCESSO, $stmt->fetchAll(PDO::FETCH_ASSOC) ); 
    }

    public function getUsuarioByLogin( $login )
    {
        $query = new DatabaseHelper( );
        $query->setSQL( "SELECT u.id, u.nome, u.senha, f.id 'funcionario_id'
                         FROM usuario u
                         LEFT JOIN funcionario f ON f.usuario_id = u.id" );
        $query->addCondicao( "u.email = ? OR u.telefone = ?", [$login, $login] );

        $retorno = $query->execute( $this->db );

        return new Retorno( Retorno::SUCESSO, $retorno->fetchAll(PDO::FETCH_ASSOC) );
    }

    public function cadastrarV1( $nome, $telefone, $email, $senha, $senha_confirmar )
    {
        $validacao = new ValidacaoHelper( );

        $validacao->vazio( "Nome obrigatório.", $nome );
        $validacao->vazio( "Telefone é obrigatório.", $telefone );
        $validacao->vazio( "Email é obrigatório.", $email );
        $temSenha = !$validacao->vazio( "Senha é obrigatório.", $senha );
        $temConfirmacao = !$validacao->vazio( "Confirmação de senha é obrigatório.", $senha_confirmar );
        
        if( $temSenha && $temConfirmacao && $senha != $senha_confirmar )
            $validacao->addErro( "As senhas não coincidem." );

        if( $this->existeUsuario( $email, $telefone ) )
            $validacao->addErro( "Usuário já cadastrado." );

        if( $validacao->temErro( ) )
            return $validacao->retorno( );

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        try
        {
            $query = new DatabaseHelper( );
            $query->setSQL( "INSERT INTO usuario (nome, email, telefone, senha) VALUES (?, ?, ?, ?)" );
            $query->addParametro( [$nome, $email, $telefone, $senhaHash] );

            $stmt = $query->execute( $this->db );

            if( $stmt )
                return new Retorno( Retorno::SUCESSO, ["mensagem" => "Cadastrado com sucesso.", "data" => ["id" => $this->db->lastInsertId()] ] );
            else
                return new Retorno( Retorno::ERRO, [ "mensagem" => "Falha ao cadastrar usuário", "data" => [] ] );
        }
        catch( Exception $e )
        {
            return new Retorno( Retorno::ERRO, [ "mensagem" => "Falha ao cadastrar usuário.", "data" => [] ] );
        }
        
    }

    public function alterar( $usuario, FiltroHelper $filtro )
    {
        $validacao = new ValidacaoHelper( );
        
        if( !$this->existeUsuarioByID( $usuario ) || 
            ( !Auth::isFuncionario( ) && $usuario !== Auth::usuario( )->id ) )
            $validacao->addErro( "Usuário inválido." );

        $parametrosValores = [];
        $parametrosCampo = [];

        if( $filtro->tem( "nome" ) )
        {
            $parametrosValores[] = $filtro->get( "nome" );
            $parametrosCampo[] = "nome = ?";
        }

        if( $filtro->tem( "senha" ) )
        {
            $senha = $filtro->get( "senha" );
            
            $parametrosValores[] = password_hash( $senha, PASSWORD_DEFAULT );
            $parametrosCampo[] = "senha = ?";
        }

        $usuarioAntigo = $this->get( $usuario );

        $email = "";
        $telefone = "";

        if( $filtro->tem( "email" ) )
        {
            $email = $filtro->get( "email" );

            $parametrosValores[] = $email;
            $parametrosCampo[] = "email = ?";

            $validacao->email( "Email inválido", $email );
        }

        if( $filtro->tem( "telefone" ) )
        {
            $telefone = $filtro->get( "telefone" );

            $validacao->vazio( "Telefone inválido.", $telefone );

            $parametrosValores[] = $telefone;
            $parametrosCampo[] = "telefone = ?";
        }

        if( !$validacao->temErro( ) )
        {
            $existe = false;

            if( $usuarioAntigo->is( Retorno::SUCESSO ) )
            {
                $usuarioAntigo = $usuarioAntigo->getMensagem( );

                if( $telefone != $usuarioAntigo['telefone'] )
                    $existe = $this->existeUsuario( "", $telefone );

                if( !$existe && $email != $usuarioAntigo['email'] )
                    $existe = $this->existeUsuario( $email, "" );

                if( $existe )
                    $validacao->addErro( "Dados inválidos." );
            }
            else
                $validacao->addErro( $usuarioAntigo->getMensagem( ) );
        }

        if( count( $parametrosCampo ) == 0 )
            $validacao->addErro( "É necessário informar um campo para ser atualizado." );

        if( $validacao->temErro( ) )
            return $validacao->retorno( );

        $query = new DatabaseHelper( );

        $update = "";

        for( $i = 0; $i < count( $parametrosCampo ); $i++ )
        {
            $campo = $parametrosCampo[$i];
             
            if( $i != 0 )
                $update .= ", $campo";
            else
                $update .= $campo;
        }

        $query->setSQL( "UPDATE usuario SET $update" );
        $query->addParametro( $parametrosValores );
        $query->addCondicao( "id = ?", $usuario );

        try
        {
            $stmt = $query->execute( $this->db );

            if( $stmt )
                return new Retorno( Retorno::SUCESSO, "Atualizado com sucesso." );
            else
                return new Retorno( Retorno::ERRO, "Falha ao alterar usuário." );
        }
        catch( Exception $e )
        {
            return new Retorno( Retorno::ERRO, "Falha ao alterar usuário." );
        }
    }

    public function get( $id )
    {
        $validacao = new ValidacaoHelper( );

        if( !$this->existeUsuarioByID( $id ) )
            $validacao->addErro( "Usuário inválido." );

        if( $validacao->temErro( ) )
            return $validacao->retorno( );
    
        try
        {
            $query = new DatabaseHelper( );
            $query->setSQL( "SELECT nome, telefone, email FROM usuario" );
            $query->addCondicao( "id = ?", $id );

            $stmt = $query->execute( $this->db );

            if( $stmt )
                 return new Retorno( Retorno::SUCESSO, $stmt->fetch( PDO::FETCH_ASSOC ) );
            else
                return new Retorno( Retorno::ERRO, "Falha ao coletar os dados do usuário." );
        }
        catch( Exception $e )
        {
            return new Retorno( Retorno::ERRO, "Falha na coleta do usuário." );
        }
    }

    public function atualizarSenha( $id, $senha, $senha_confirmar )
    {
        $validacao = new ValidacaoHelper( );

        if( $senha !== $senha_confirmar )
            $validacao->addErro( "As senhas não se coincidem." );
    
        if( $validacao->temErro( ) )
            return $validacao->retorno( );

        $filtro = new FiltroHelper( null );
        $filtro->addFiltro( "senha", $senha );

        return $this->alterar( $id, $filtro );
    } 
}