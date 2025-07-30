<?php
namespace Vennizlab\Agendaki\models;

use PDO;
use Vennizlab\Agendaki\core\Model;

class ParametrosModel extends Model{
    
    const agenda_permite_livre_digita_horario = "agenda_permite_livre_digita_horario";
    const agenda_minutos_intervalo = "agenda_minutos_intervalo";
    
    public function editarParametro( $nome, $valor )
    {
        $stmt = $this->db->prepare("UPDATE parametros SET valor = ? WHERE nome = ? ");
        return $stmt->execute( [$valor, $nome] );
    }

    public function getParametro( $nome )
    {
        $stmt = $this->db->prepare("SELECT * FROM parametros WHERE nome = ?");
        $stmt->execute([$nome]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}