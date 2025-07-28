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

    public function addFuncionarioServico( $id, $servicos )
    {
        $parametros = "";
        $parametros_valores = [];

        for( $i = 0; $i < count($servicos);$i++ )
        {
            if( $i == 0 )
                $parametros .= " VALUES (?, ?)";
            else
                $parametros .= ", (?, ?)";
        }

        foreach( $servicos as $servico )
        {
            array_push($parametros_valores, $servico);
            array_push($parametros_valores, $id);
        }

        $stmt = $this->db->prepare("INSERT INTO funcionario_servico (servico_id, funcionario_id) $parametros");
        $sucesso = $stmt->execute($parametros_valores);

        return $sucesso;
    }

    public function getByFuncionario( $id )
    {
        $stmt = $this->db->prepare( "SELECT s.* FROM servico s
                                    INNER JOIN funcionario_servico fs ON fs.servico_id = s.id
                                    WHERE fs.funcionario_id = ? ");

        $sucesso = $stmt->execute([$id]);

        if( $sucesso )
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        else
            return [];
    }

    public function existeServico( $servicos )
    {
        $parametros = "";

        for( $i = 0; $i < count($servicos); $i++ )
        {
            if( $i == 0 )
                $parametros .= " ?";
            else
                $parametros .= ", ?";
        }

        $stmt = $this->db->prepare( "SELECT COUNT(1) FROM servico WHERE id IN ($parametros)");
        $stmt->execute($servicos);

        $resultado = $stmt->fetchColumn();

        if( !$resultado || $resultado != count($servicos) )
            return false;
        else
            return true;
    }

    public function existeServicoFuncionario( $funcionario, $servico )
    {
        $servicos = array_column($this->getByFuncionario($funcionario), 'id');
        return count(array_diff($servico, $servicos)) == 0;
    }
}