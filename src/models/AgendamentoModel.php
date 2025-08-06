<?php
namespace Vennizlab\Agendaki\models;

use PDO;
use Vennizlab\Agendaki\core\Model;

class AgendamentoModel extends Model
{
    public function getAll( )
    {
        $stmt = $this->db->prepare("SELECT * FROM agendamento a");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByAgenda( $id )
    {
        $stmt = $this->db->prepare("SELECT a.*, fs.duracao, ag.inicio 'agenda_inicio', ag.fim 'agenda_fim', sa.inicio 'servico_inicio', sa.fim 'servico_fim' 
                                    FROM agendamento a
                                    INNER JOIN agenda_servico sa ON sa.id = a.agenda_servico_id
                                    INNER JOIN agenda ag ON ag.id = sa.agenda_id
                                    INNER JOIN servico s ON s.id = sa.servico_id
                                    INNER JOIN funcionario_servico fs ON fs.funcionario_id = ag.funcionario_id
                                                                      AND fs.servico_id = s.id 
                                    WHERE ag.id = ?
                                    ORDER BY a.inicio");
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByID( $id )
    {
        $stmt = $this->db->prepare( "SELECT a.*, fs.duracao
                                     FROM agenda_servico a
                                     INNER JOIN agenda ag ON ag.id = a.agenda_id
                                     INNER JOIN funcionario_servico fs ON fs.funcionario_id = ag.funcionario_id
                                                                       AND fs.servico_id = a.servico_id
                                     WHERE a.id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrar( $agenda_servico, $usuario_id, $inicio, $fim )
    {
        $usuarioModel = new UsuarioModel( );

        if( $usuarioModel->existeUsuarioByID( $usuario_id ) && !$this->existeAgendamento( $agenda_servico, $inicio, $fim )  )
        {
            $stmt = $this->db->prepare("INSERT INTO agendamento (agenda_servico_id, usuario_id, inicio, fim) VALUES (?, ?, ?, ?)");
            $sucesso = $stmt->execute([$agenda_servico, $usuario_id, $inicio, $fim]);

            return $sucesso;
        }
        else
            return false;
    }

    public function existeAgendamento( $agenda_servico, $inicio, $fim )
    {
        return false;

        $stmt = $this->db->prepare( "SELECT 1 
                                     FROM agendamento a
                                     INNER JOIN agenda_servico sa ON sa.id = a.agenda_servico_id
                                     INNER JOIN agenda ag ON ag.id = sa.agenda_id
                                     WHERE sa.id = ? 
                                     AND ( a.inicio BETWEEN ? AND ? OR a.fim BETWEEN ? AND ? )
                                     AND ( ag.inicio BETWEEN ? AND ? OR ag.fim BETWEEN ? AND ? ) ");
        $stmt->execute([$agenda_servico,
                        $inicio, $fim, $inicio, $fim,
                        $inicio, $fim, $inicio, $fim]);

        return $stmt->fetchColumn() !== false;
    }

    public function getAgendamentos( $data, $funcionarios, $servicos, $usuarios )
    {
        $where = [];
        $parametros_valores = [];

        if( $data )
        {
            array_push($where, "ag.data = ?");
            array_push($parametros_valores, $data);
        }

        if( $funcionarios )
        {
            $parametros = $this->getParametros( $funcionarios );
            array_push($where, "ag.funcionario_id IN ( $parametros )");

            $parametros_valores = array_merge($parametros_valores, $funcionarios);
        }

        if( $servicos )
        {
            $parametros = $this->getParametros( $servicos );
            array_push($where, "sa.servico_id IN ( $parametros )");

            $parametros_valores = array_merge($parametros_valores, $servicos);
        }

        if( $usuarios )
        {
            $parametros = $this->getParametros( $usuarios );
            array_push($where, "a.usuario_id IN ( $parametros )");

            $parametros_valores = array_merge($parametros_valores, $usuarios);
        }

        $sql = "SELECT uf.Nome 'Nome_Funcionario', u.Nome 'Nome', u.Telefone 'Telefone', ag.data 'Data', a.inicio 'Inicio_Agendamento', a.fim 'Fim_Agendamento',
                s.nome 'Nome_Servico'
                FROM agendamento a
                INNER JOIN agenda_servico sa ON sa.id = a.agenda_servico_id
                INNER JOIN agenda ag ON ag.id = sa.agenda_id
                INNER JOIN servico s ON s.id = sa.servico_id
                INNER JOIN usuario u ON u.id = a.usuario_id
                INNER JOIN funcionario f ON f.id = ag.funcionario_id
                INNER JOIN usuario uf ON uf.id = f.usuario_id";

        for( $i = 0; $i < count($where); $i++)
            $sql .= ( $i == 0 ? " WHERE " : " AND " ) . $where[$i];

        $sql .= " ORDER BY ag.data, a.inicio, a.fim";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($parametros_valores);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}