<?php

namespace Vennizlab\Agendaki\models;

use Firebase\JWT\JWT;
use Vennizlab\Agendaki\core\Model;
use Vennizlab\Agendaki\core\Retorno;
use Vennizlab\Agendaki\helpers\ValidacaoHelper;

class AuthModel extends Model{
    public function login( $login, $senha )
    {
        $validacao = new ValidacaoHelper( );

        $validacao->vazio( "", $login );
        $validacao->vazio( "", $senha );

        $usuarioModel = new UsuarioModel( );
        $usuario = $usuarioModel->getUsuarioByLogin( $login );

        if( $usuario->is( Retorno::SUCESSO ) )
        {
            $usuario = $usuario->getMensagem( );
            if( !$validacao->vazio( "", $usuario ) )
                $usuario = $usuario[0];
        }
        else
            $validacao->addErro( "erro" );

        if( !$validacao->temErro( ) && password_verify( $senha, $usuario['senha'] ) )
        {
            $funcionario = isset( $usuario['funcionario_id'] );
            $permissao = [];

            if( $funcionario )
            {
                $permissaoModel = new PermissaoModel( );
                $retorno = $permissaoModel->getPermissaoFuncionario( $funcionario );

                if( $retorno->is( Retorno::SUCESSO ) )
                    foreach( $retorno->getMensagem( ) as $p )
                        $permissao[] = $p['id'];
            }

            $payload = [
                "iss" => "http://localhost:8000", // Emissor
                "aud" => "http://localhost:8000", // Público
                "iat" => time(),
                "exp" => time() + 3600,
                "data" => [
                    "id" => $usuario['id'],
                    "nome" => $usuario['nome'],
                    "permissoes" => $permissao,
                    "funcionario" => $funcionario,
                    'funcionario_id' => $usuario['funcionario_id']
                ]
            ];

            $jwt = JWT::encode( $payload, $this->config['secret_key'], 'HS256');

            return new Retorno( Retorno::SUCESSO, ["mensagem" => "Login realizado com sucesso.", "token" => $jwt]);
        }
        else
            return new Retorno( Retorno::ERRO_VALIDACAO, "Usuário ou senha inválida.");
    }
}