<?php
namespace Vennizlab\Agendaki\models;

use DateInterval;
use DateTime;
use Exception;
use PDO;
use Vennizlab\Agendaki\core\Model;
use Vennizlab\Agendaki\core\Retorno;
use Vennizlab\Agendaki\helpers\ValidacaoHelper;

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

    public function cadastrarV1( $agenda_servico, $usuario_id, $inicio )
    {
        $fim = null;

        $validacao = new ValidacaoHelper( );

        $usuarioModel = new UsuarioModel( );

        if( !$usuarioModel->existeUsuarioByID( $usuario_id ) )
            $validacao->addErro( "-Usuário não existe." );

        if( !$validacao->vazio( "-Inicio é obrigatório.", $inicio ) )
            $validacao->validaHorario( "-Formato do inicio inválido.", $inicio );
    
        if( !$validacao->vazio( "-agenda_servico é obrigatório.", $agenda_servico ) )
        {
            $agenda = $this->getByID( $agenda_servico );
            if( !$validacao->vazio( "-agenda_servico não encontrado.", $agenda ) ) 
            {
                $fim = new DateTime($inicio);
                $duracao = $agenda['duracao'];
                list($h, $m, $s) = explode(':', $duracao);
                $duracaoInterval = new DateInterval("PT{$h}H{$m}M");
                $fim->add($duracaoInterval);
                $fim = $fim->format("H:i");

                $horarios = $this->getHorariosDisponiveisServicoAgenda( $agenda_servico );

                if( $horarios->is(Retorno::SUCESSO))
                {
                    $horarioValido = false;
                    $inicioTime = new DateTime( $inicio );

                    foreach( $horarios->getMensagem() as $horario)
                    {
                        $inicioIntervalo = new DateTime( $horario['inicio'] );
                        $fimIntervalo = new DateTime( $horario['fim'] );

                        if( $inicioTime >= $inicioIntervalo && $inicioTime <= $fimIntervalo )
                        {
                            $horarioValido = true;
                            break;
                        }
                    }

                    if( !$horarioValido )
                        $validacao->addErro( "- Horário de início inválido.");
                }
                else
                    $validacao->addErro( $horarios->getMensagem( ) );
            }
        }

        if( $validacao->temErro( ) )
            return new Retorno( Retorno::ERRO_VALIDACAO, $validacao->getValidacao( ) );
        
        $stmt = $this->db->prepare( "SELECT hsv.valor 
                                    FROM agenda_servico sa
                                    INNER JOIN agenda a ON a.id = sa.agenda_id
                                    INNER JOIN servico s ON s.id = sa.servico_id
                                    INNER JOIN historico_servico_valor hsv ON hsv.servico_id = s.id
                                                                           AND hsv.data <= a.data
                                    WHERE sa.id = ?
                                    ORDER BY hsv.data DESC
                                    LIMIT 1" );
                                    
        $stmt->execute( [$agenda_servico] );
        $valor = $stmt->fetchColumn() ?: 0.0;

        try
        {
            $this->db->beginTransaction( );

            $stmt = $this->db->prepare("INSERT INTO agendamento (agenda_servico_id, usuario_id, inicio, fim, valor) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$agenda_servico, $usuario_id, $inicio, $fim, $valor]);

            $this->db->commit( );
            return new Retorno( Retorno::SUCESSO, "Agendamento cadastrado com sucesso.");
        }
        catch( Exception $e )
        {
            $this->db->rollBack();
            return new Retorno( Retorno::ERRO, "Falha ao cadastrar o agendamento: " . $e->getMessage( ) );
        }
    }

    public function existeAgendamento( $agenda_servico, $inicio, $fim )
    {
        $stmt = $this->db->prepare( " SELECT 1 
                                      FROM agenda a
                                      INNER JOIN agenda_servico sa ON sa.agenda_id = a.id
                                      INNER JOIN agendamento ag ON ag.agenda_servico_id = sa.id
                                      WHERE a.id = ( SELECT agenda_id FROM agenda_servico WHERE id = ? LIMIT 1) 
                                      AND ( (ag.inicio >= ? AND ag.inicio < ?) OR
                                            (ag.fim > ? AND ag.fim <= ?) OR
                                            (ag.inicio > ? AND ag.fim < ?) OR
                                            (ag.inicio <= ? AND ag.fim >= ?) )
                                      LIMIT 1" );

        $stmt->execute([$agenda_servico,
                        $inicio, $fim, 
                        $inicio, $fim,
                        $inicio, $fim,
                        $inicio, $fim]);

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

        $sql = "SELECT sa.id, uf.Nome 'Nome_Funcionario', u.Nome 'Nome', u.Telefone 'Telefone', ag.data 'Data', a.inicio 'Inicio_Agendamento', a.fim 'Fim_Agendamento',
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

    public function getHorariosDisponiveisServicoAgenda( $id )
    {
        if( empty($id) )
            return new Retorno( Retorno::ERRO_VALIDACAO, "-ID é obrigatório.");

        $agenda_servico = $this->getByID( $id );

        if( !$agenda_servico )
            return new Retorno( Retorno::ERRO, '- Agenda de serviço não encontrada.');

        $agendamentos = $this->getByAgenda( $agenda_servico['agenda_id'] );
        
        $inicio = $agenda_servico['inicio'];
        $fim = $agenda_servico['fim'];
        $duracao = $agenda_servico['duracao'];

        $inicio = new DateTime($agenda_servico['inicio']);
        $fim = new DateTime($agenda_servico['fim']);
        $duracao = $agenda_servico['duracao'] ?? "00:00:00";

        $duracaoInterval = new DateInterval('PT' . (new DateTime($duracao))->format('H') . 'H' . (new DateTime($duracao))->format('i') . 'M');
        $duracaoSegundos = ($duracaoInterval->h * 3600) + ($duracaoInterval->i * 60) + $duracaoInterval->s;

        $intervalosDisponiveis = [];

        usort($agendamentos, function ($a, $b) {
            return strtotime($a['inicio']) - strtotime($b['inicio']);
        });

        $ultimoFim = clone $inicio;

        foreach ($agendamentos as $agendamento) {
            $inicioAgendamento = new DateTime($agendamento['inicio']);
            $fimAgendamento = new DateTime($agendamento['fim']);

            $limiteInicio = (clone $inicioAgendamento)->sub($duracaoInterval);

            if ($limiteInicio > $ultimoFim) {
                $intervaloSegundos = $limiteInicio->getTimestamp() - $ultimoFim->getTimestamp();

                if ($intervaloSegundos >= $duracaoSegundos) {
                    $intervalosDisponiveis[] = [
                        'inicio' => $ultimoFim->format('H:i'),
                        'fim'    => $limiteInicio->format('H:i'),
                    ];
                }
            }

            if ($fimAgendamento > $ultimoFim) {
                $ultimoFim = $fimAgendamento;
            }
        }

        if ($ultimoFim < $fim) {
            $intervaloFinalSegundos = $fim->getTimestamp() - $ultimoFim->getTimestamp();

            if ($intervaloFinalSegundos >= $duracaoSegundos) {
                $intervalosDisponiveis[] = [
                    'inicio' => $ultimoFim->format('H:i'),
                    'fim'    => $fim->format('H:i'),
                ];
            }
        }

        return new Retorno( Retorno::SUCESSO, $intervalosDisponiveis );
    }

}