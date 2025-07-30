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

}