<?php
namespace Vennizlab\Agendaki\models;

use Exception;
use PDO;
use Vennizlab\Agendaki\core\Model;
use Vennizlab\Agendaki\core\Retorno;
use Vennizlab\Agendaki\helpers\DatabaseHelper;
use Vennizlab\Agendaki\helpers\FiltroHelper;
use Vennizlab\Agendaki\helpers\Paginacao;
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

    public function cadastrarV1( $nome, $descricao, $preco, $data, $preco_inicio, $preco_fim, $duracao )
    {
        $validacao = new ValidacaoHelper( );
        
        if( !$validacao->vazio( "Nome é obrigatório.", $nome ) )
            if( $this->getByNome( $nome ) )
                $validacao->addErro( "Serviço com o nome ( $nome ) já cadastrado." );

        $validacao->vazio( "Preco é obrigatório.", $preco );

        if( $validacao->temErro( ) )
            return $validacao->retorno( );

        $stmt = $this->db->prepare( "INSERT INTO servico (nome, descricao, duracao) VALUES (?,?,?)" );
        $retorno = $stmt->execute( [$nome, $descricao, $duracao] );

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

    public function cadastrarPreco( $id, $data = null , $preco = 0.0, $inicio = "00:00", $fim = "23:59" )
    {
        if( !isset( $data ) )
            $data = date( "Y-m-d" );

        $validacao = new ValidacaoHelper( );
        $validacao->vazio("- ID inválido", $id );

        if( !$validacao->temErro( ) )
        {
            $precos = $this->getPreco( $id, $data, $inicio, $fim );

            if( !$precos->is(Retorno::SUCESSO))
                $validacao->addErro($precos->getMensagem());
            else
                $validacao->naoVazio( "- Já existe preço cadastrado para essa data e horário.", $precos->getMensagem( ) );
        }
        
        if( $validacao->temErro( ) )
            return new Retorno( Retorno::ERRO_VALIDACAO, $validacao->getValidacao( ) );

        $stmt = $this->db->prepare( "INSERT INTO historico_servico_valor (data, servico_id, valor, inicio, fim) VALUES (?,?,?,?,?)");
        $retorno = $stmt->execute( [$data, $id, $preco, $inicio, $fim] );

        if( $retorno )
            return new Retorno( Retorno::SUCESSO, "Preço cadastrado com sucesso.");
        else
            return new Retorno( Retorno::ERRO, "Falha ao cadastrar o preço do serviço.");
    }

    public function getPreco( $id_servico = null, $data = null, $inicio = null, $fim = null, $id = null )
    {
        $validacao = new ValidacaoHelper( );

        if( isset( $inicio ) )
            $validacao->validaHorario( "- Formato de inicio inválido.", $inicio );
    
        if( isset( $fim ))
            $validacao->validaHorario( "- Formato de fim inválido.", $fim );

        if( isset( $data ) )
            $validacao->data( "- Formato de data inválido.", $data );

        if( $validacao->temErro( ) )
            return new Retorno( Retorno::ERRO_VALIDACAO, $validacao->getValidacao() );

        $query = new DatabaseHelper( );
        $query->setSQL( " SELECT hsv.id, hsv.servico_id, hsv.data, hsv.inicio, hsv.fim, hsv.valor, s.Nome 
                          FROM historico_servico_valor hsv
                          INNER JOIN servico s ON s.id = hsv.servico_id " );

        if( isset( $id ) )
            $query->addCondicao( "hsv.id = ?", $id );

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

    public function getPaginado( FiltroHelper $filtro, ?Paginacao $paginacao = null )
    {
        $query = new DatabaseHelper( );
        $query->setSQL( "SELECT s.*, IFNULL( hsv.valor, 0.0 ) 'preco' 
                         FROM servico s
                         LEFT JOIN historico_servico_valor hsv ON hsv.servico_id = s.id
                                                               AND hsv.id = ( SELECT id FROM historico_servico_valor WHERE servico_id = s.id ORDER BY data DESC, fim DESC LIMIT 1)" );
        $query->setPaginacao( $paginacao );

        if( $filtro->tem( "id" ) )
            $query->addCondicao( "s.id = ?", $filtro->get( "id" ) );

        if( !$filtro->tem( "inativo" ) )
            $query->addCondicao( "s.ativo != ?", 0 );

        $stmt = $query->execute( $this->db );
        
        return new Retorno( Retorno::SUCESSO, $stmt->fetchAll( PDO::FETCH_ASSOC ) );
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

    public function editarPreco( $id, $preco = 0.0, $inicio = "00:00", $fim = "23:59" )
    {
        $validacao = new ValidacaoHelper( );
        $validacao->vazio( "-ID inválido.", $id );

        if( !empty( $id ) )
        {
            $precos = $this->getPreco( null, null, null, null, $id );

            if( !$precos->is(Retorno::SUCESSO))
                $validacao->addErro( $precos->getMensagem( ) );
            else
                $validacao->vazio( "-Preço não encontrado.", $precos->getMensagem( ) );
        }

        $validacao->validaHorario( "-Formato de início inválido.", $inicio );
        $validacao->validaHorario( "-Formato de fim inválido.", $fim );
        
        if( $validacao->temErro( ) )
            return new Retorno( Retorno::ERRO_VALIDACAO, $validacao->getValidacao( ) );

        $stmt = $this->db->prepare( "UPDATE historico_servico_valor SET valor = ?, inicio = ?, fim = ? WHERE id = ?" );
        $retorno = $stmt->execute( [$preco, $inicio, $fim, $id] );

        if( $retorno )
            return new Retorno( Retorno::SUCESSO, "Preço atualizado com sucesso." );
        else
            return new Retorno( Retorno::ERRO, "Falha ao atualizar o preço.");
    }

    public function get( $id )
    {
        $query = new DatabaseHelper( );
        $query->setSQL( "SELECT s.*, IFNULL( hsv.valor, 0.0 ) 'preco' 
                         FROM servico s
                         LEFT JOIN historico_servico_valor hsv ON hsv.servico_id = s.id
                                                               AND hsv.id = ( SELECT id FROM historico_servico_valor WHERE servico_id = s.id ORDER BY data DESC, fim DESC LIMIT 1)" );

        $parametros = $this->getParametros($id);

        $query->addCondicao( "s.id IN ($parametros)", $id );

        $retorno = $query->execute( $this->db );

        if( $retorno )
            return new Retorno( Retorno::SUCESSO, $retorno->fetchAll(PDO::FETCH_ASSOC) );
        else
            return new Retorno( Retorno::ERRO, "Falha ao listar os serviços." );
    }

    public function editar( $id, $nome, $descricao, $ativo )
    {
        $validacao = new ValidacaoHelper( );
        $validacao->vazio( "ID é obrigatório.", $id );
        $validacao->vazio( "Nome é obrigatório.", $nome );

        if( $validacao->temErro( ) )
            return $validacao->retorno( );

        $query = new DatabaseHelper( );
        $query->setSQL( "UPDATE servico SET Nome = ?, Descricao = ?, Ativo = ? WHERE id = ?" );
        $query->addParametro( [$nome, $descricao, $ativo, $id] );

        $stmt = $query->execute( $this->db );
        
        if( $stmt )
            return new Retorno( Retorno::SUCESSO, "Serviço alterado com sucesso." );
        else
            return new Retorno( Retorno::ERRO, "Falha ao alterar o serviço." );
    }

    public function inativar( $id )
    {
        $validacao = new ValidacaoHelper( );
        
        if( !$validacao->nulo( "ID é obrigatório.", $id ) )
        {
            $retorno = $this->get( $id );

            if( !$validacao->erroRetorno( $retorno ) )
                $validacao->vazio( "Serviço não encontrado.", $retorno->getMensagem( ) );
        }

        if( $validacao->temErro( ) )
            return $validacao->retorno( );

        try
        {
            $query = new DatabaseHelper( );
            $query->setSQL( "UPDATE servico SET ativo = 0 WHERE id = ?" );
            $query->addParametro( [$id] );

            $stmt = $query->execute( $this->db );

            if( $stmt )
                return new Retorno( Retorno::SUCESSO, [ "mensagem" => "Serviço inativado com sucesso." ] );
            else
                return new Retorno( Retorno::ERRO, [ "mensagem" => "Falha ao inativar o serviço." ] );
        }
        catch( Exception $e )
        {
            return new Retorno( Retorno::ERRO, [ "mensagem" => "Falha ao inativar o serviço: ", $e->getMessage( ) ]  );
        }
        
    }
}