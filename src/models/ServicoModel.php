<?php
namespace Vennizlab\Agendaki\models;

use Dotenv\Validator;
use PDO;
use Vennizlab\Agendaki\core\Model;
use Vennizlab\Agendaki\core\Retorno;
use Vennizlab\Agendaki\helpers\DatabaseHelper;
use Vennizlab\Agendaki\helpers\ValidacaoHelper;

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

    public function cadastrarV1( $nome, $descricao, $preco, $data, $preco_inicio, $preco_fim )
    {
        $validacao = [];

        if( !$nome )
            array_push( $validacao, "Nome é obrigatório" );
        else if( $this->getByNome( $nome ) )
            array_push( $validacao, "Serviço com o nome ( $nome ) já cadastrado." );

        if( !empty($validacao) )
            return new Retorno( Retorno::ERRO_VALIDACAO, $validacao );

        $stmt = $this->db->prepare( "INSERT INTO servico (nome, descricao) VALUES (?,?)" );
        $retorno = $stmt->execute( [$nome, $descricao] );

        if( $retorno )
        {
            $id = $this->db->lastInsertId( );

            $retorno = $this->cadastrarPreco( $id, $data, $preco, $preco_inicio, $preco_fim );

            if( $retorno->is( Retorno::SUCESSO ) )
                return new Retorno( Retorno::SUCESSO, ["Serviço cadastrado com sucesso."]);
            else
            {
                $this->db->prepare( "DELETE FROM servico WHERE id = ?")->execute( [$id]);

                $mensagem = $retorno->getMensagem( );
                $mensagem[] = "Falha ao cadastrar o preço do serviço.";

                return new Retorno( Retorno::ERRO, $mensagem);
            }
        }
        else
            return new Retorno( Retorno::ERRO, ["Falha ao cadastrar o serviço."] );
    }

    public function cadastrarPreco( $id, $data = null , $preco = 0.0, $preco_inicio = "00:00", $preco_fim = "23:59" )
    {
        if( !isset( $data ) )
            $data = date( "Y-m-d" );

        $validacao = new ValidacaoHelper( );
        $validacao->vazio("- ID inválido", $id );
        $validacao->data("- Data inválida", $data );

        $precos = $this->getPreco( $id, $data, $preco_inicio, $preco_fim );

        if( !$precos->is(Retorno::SUCESSO))
            $validacao->addErro($precos->getMensagem());
        else
            $validacao->naoVazio( "- Já existe preço cadastrado para essa data e horário.", $precos->getMensagem( ) );

        if( $validacao->temErro( ) )
            return new Retorno( Retorno::ERRO_VALIDACAO, $validacao->getValidacao( ) );

        $stmt = $this->db->prepare( "INSERT INTO historico_servico_valor (data, servico_id, valor) VALUES (?,?,?)");
        $retorno = $stmt->execute( [$data, $id, $preco] );

        if( $retorno )
            return new Retorno( Retorno::SUCESSO, "Preço cadastrado com sucesso.");
        else
            return new Retorno( Retorno::ERRO, "Falha ao cadastrar o preço do serviço.");
    }

    public function getPreco( $id_servico, $data, $inicio, $fim )
    {
        $validacao = new ValidacaoHelper( );

        $validacao->validaHorario( "- Formato de inicio inválido.", $inicio );
        $validacao->validaHorario( "- Formato de fim inválido.", $fim );

        if( $validacao->temErro( ) )
            return new Retorno( Retorno::ERRO_VALIDACAO, $validacao->getValidacao() );

        $query = new DatabaseHelper( );
        $query->setSQL( " SELECT hsv.id, hsv.servico_id, hsv.data, hsv.inicio, hsv.fim, hsv.valor, s.Nome 
                          FROM historico_servico_valor hsv
                          INNER JOIN servico s ON s.id = hsv.servico_id " );

        if( isset($id_servico) )
            $query->addCondicao( "hsv.servico_id = ?", $id_servico );

        if( isset($data) )
            $query->addCondicao( "hsv.data = ?", $data);

        if( isset($inicio) )
            $query->addCondicao( "hsv.inicio >= ?", $inicio );

        if( isset($fim) )
            $query->addCondicao( "hsv.fim <= ?", $fim );

        $stmt = $this->db->prepare( $query->getSQL( ) );
        $retorno = $stmt->execute( $query->getParametros( ) );

        if( $retorno )
            return new Retorno( Retorno::SUCESSO, $stmt->fetchAll( PDO::FETCH_ASSOC ) );
        else
            return new Retorno( Retorno::ERRO, "Falha ao consultar o preço.");
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

    public function addFuncionarioServico( $id, $servicos, $duracao )
    {
        $parametros = "";
        $parametros_valores = [];

        for( $i = 0; $i < count($servicos);$i++ )
        {
            if( $i == 0 )
                $parametros .= " VALUES (?, ?, ?)";
            else
                $parametros .= ", (?, ?, ?)";

            array_push($parametros_valores, $servicos[$i]);
            array_push($parametros_valores, $id);
            array_push($parametros_valores, $duracao[$i]);
        }

        $stmt = $this->db->prepare("INSERT INTO funcionario_servico (servico_id, funcionario_id, duracao) $parametros");
        $sucesso = $stmt->execute($parametros_valores);

        return $sucesso;
    }

    public function getByFuncionario( $id )
    {
        $stmt = $this->db->prepare( "SELECT s.*, fs.duracao FROM servico s
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

    public function atualizaServicoFuncionario( $funcionario, $servicos, $duracao )
    {
        $servicosFuncionario = array_column($this->getByFuncionario($funcionario), 'id');
        $sucesso = true;

        if( !empty($servicosFuncionario) )
            $sucesso = $this->removerServicoFuncionario( $funcionario, $servicosFuncionario );

        if( $sucesso )
            $sucesso = $this->addFuncionarioServico( $funcionario, $servicos, $duracao );

        return $sucesso;
    }

    public function removerServicoFuncionario( $funcionario_id, $servicos)
    {
        if( empty($servicos) )
            return false;

        $parametros = "";
        for( $i = 0; $i < count($servicos); $i++ )
        {
            if( $i == 0 )
                $parametros .= " ?";
            else
                $parametros .= ", ?";
        }

        $parametros_valores = array_merge([$funcionario_id], $servicos);

        $stmt = $this->db->prepare("DELETE FROM funcionario_servico WHERE funcionario_id = ? and servico_id IN ($parametros) ");
        $retorno = $stmt->execute( $parametros_valores );

        return $retorno;
    }
}