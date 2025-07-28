<?php
namespace Vennizlab\Agendaki\models;

use PDO;
use Vennizlab\Agendaki\core\Model;

class AgendaModel extends Model{

    public function cadastrar( )
    {
        $data = $_POST['data_agenda'];
        $inicio = $_POST['inicio_agenda'];
        $fim = $_POST['fim_agenda'];
        $funcionario_id = $_POST['funcionario_id_agenda'];
        $servico = $_POST['servico'];

        if( !$data || !$inicio || !$fim || !$funcionario_id || !$servico )
            return ["erro" => "Todos os campos são obrigatórios."];

        if( $fim <= $inicio )
            return ["erro" => "O Fim não pode ser inferior ou igual ao início."];

        $funcionario_model = new FuncionarioModel( );
        $funcionario = $funcionario_model->getById( $funcionario_id );

        if( !$funcionario )
            return ["erro" => "Funcionário não encontrado."];

        if( $this->existeAgenda($data, $inicio, $fim, $funcionario_id) )
            return ["erro" => "Já existe agenda cadastrado para $data, no intervalo de $inicio e $fim"];

        $servicoModel = new ServicoModel( );
        
        if( !$servicoModel->existeServicoFuncionario( $funcionario_id, $servico ) )
            return ["erro" => "Existem serviços que não foram encontrados."];
        
        $stmt = $this->db->prepare( "INSERT INTO agenda (funcionario_id, data, inicio, fim)VALUES (?,?,?,?)");
        $retorno = $stmt->execute([$funcionario_id, $data, $inicio, $fim]);

        if( $retorno )
        {
            $id = $this->db->lastInsertId( );

            $parametros = "";
            $parametros_valores = [];

            for( $i = 0; $i < count($servico); $i++ )
            {
                if( $i == 0 )
                    $parametros .= " VALUES (?, ?)";
                else
                    $parametros .= ", (?, ?)";
            }

            foreach( $servico as $s )
            {   
                array_push($parametros_valores, $id);
                array_push($parametros_valores, $s);
            }

            $stmt = $this->db->prepare( "INSERT INTO agenda_servico (agenda_id, servico_id) $parametros");
            $retorno = $stmt->execute($parametros_valores);

            if( $retorno )
                return ['sucesso' => "Agenda cadastrada com sucesso."];
        }

        return ['erro' => "Falha ao cadastrar a agenda."];
    }

    private function existeAgenda( $data, $inicio, $fim, $funcionario_id )
    {
        $stmt = $this->db->prepare( "SELECT 1 FROM agenda where funcionario_id = ? and data = ? and ( inicio BETWEEN ? AND ? OR fim BETWEEN ? AND ?)");
        $stmt->execute([$funcionario_id, $data, $inicio, $fim, $inicio, $fim]);
        return $stmt->fetchColumn() !== false;
    }

    public function getAgenda(  )
    {
        $data = $_POST['data'] ?? null;
        $funcionario = $_POST['funcionario'] ?? null;

        $data = empty($data) ? null : $data;
        $funcionario = empty($funcionario) ? null :$funcionario;

        if( $funcionario <= 0 )
            $funcionario = null;

        $condicoes = [];
        

        if( isset($data) )
            array_push($condicoes, ["condicao" => "data = ?", "valor" => $data]);
        if( isset($funcionario) )
            array_push($condicoes, ["condicao" => "funcionario_id = ?", "valor" => $funcionario ]);

        $where = "";
        $valores = [];
        if( count($condicoes) > 0 )
        {
            for( $i = 0; $i < count($condicoes); $i++ )
            {
                $condicao = $condicoes[$i]["condicao"];
                array_push( $valores, $condicoes[$i]["valor"]);

                if( $i == 0 )
                    $where .= " WHERE $condicao";
                else
                    $where .= " AND $condicao";
            }
        }

        $stmt = $this->db->prepare( "SELECT u.nome, a.data, a.inicio, a.fim 
                                     FROM agenda a
                                     INNER JOIN funcionario f ON f.id = a.funcionario_id
                                     INNER JOIN usuario u ON u.id = f.usuario_id
                                     $where" );
        $stmt->execute($valores);
        $agendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $agendas;
    }
}