<?php

namespace Vennizlab\Agendaki\models;

use PDO;
use Vennizlab\Agendaki\core\Model;

class UsuarioModel extends Model{

    public function all( )
    {
        $stmt = $this->db->query("SELECT * FROM usuario");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function existeUsuario($email, $telefone) {
        $stmt = $this->db->prepare("SELECT 1 FROM usuario WHERE telefone = ? OR email = ? LIMIT 1");
        $stmt->execute([$telefone, $email]);
        return $stmt->fetchColumn() !== false;
    }
}