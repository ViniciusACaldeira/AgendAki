<?php
namespace Vennizlab\Agendaki\models;

use PDO;
use Vennizlab\Agendaki\core\Model;

class ServicoModel extends Model{

    public function cadastrar( )
    {
        $nome = $_POST['nome_servico'] ?? null;
        $descricao = $_POST['descricao_servico'] ?? '';

        if(!$nome)
            return ['erro' => 'Nome é obrigatório.'];

        if( $this->getByNome( $nome ) )
            return ['erro' => 'Serviço com o nome ('.$nome.') já cadastrado.'];

        $stmt = $this->db->prepare("INSERT INTO servico (nome, descricao) VALUES (?,?)");
        $retorno = $stmt->execute( [$nome, $descricao] );

        if( $retorno )
            return ['sucesso' => "Serviço $nome cadastrado com sucesso."];
        else
            return ['erro' => "falha ao cadastrar o serviço $nome."];
    }

    public function getByNome( $nome )
    {
        $stmt = $this->db->prepare("SELECT * FROM servico WHERE nome = ? ");
        $stmt->execute( [$nome] );

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll( )
    {
        $stmt = $this->db->prepare("SELECT * FROM servico");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}