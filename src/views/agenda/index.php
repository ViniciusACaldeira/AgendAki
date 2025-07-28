<?php
    use Vennizlab\Agendaki\helpers\Flash;
    use Vennizlab\Agendaki\models\AgendaModel;
use Vennizlab\Agendaki\models\FuncionarioModel;

    Flash::print( ); 
?>

<a href="/dashboard">Voltar</a>

<h1>Agenda</h1>
<a href="/agenda/cadastro">Cadastrar Agenda</a>

<?php

$agendaModel = new AgendaModel( );

?>

<form action="/agenda/listar" method="POST">
    <label for="data">Data:</label>
    <input type="date" name="data" id="data">

    <label for="funcionario">Funcionario</label>
    <select name="funcionario" id="funcionario">
        <option value="0">Nenhum</option>
        <?php
            $funcionarioModel = new FuncionarioModel( );
            $funcionarios = $funcionarioModel->getAll();

            foreach( $funcionarios as $funcionario )
                echo "<option value='".$funcionario['id']."'>".$funcionario['nome']."</option>";
        ?>
    </select>

    <button type="submit">Buscar</button>
</form>

<table>
    <thead>Agendas</thead>
    <th>Funcionário</th>
    <th>Data</th>
    <th>Início</th>
    <th>Fim</th>
    <tbody>
        <?php
            if(isset($agendas))
            {
                foreach( $agendas as $agenda )
                {
                    echo "<tr>";
                    echo "    <td>".$agenda['nome']."</td>";
                    echo "    <td>".$agenda['data']."</td>";
                    echo "    <td>".$agenda['inicio']."</td>";
                    echo "    <td>".$agenda['fim']."</td>";
                    echo "</tr>";
                }
            }
            else
            {
                echo "<tr><td coll='4'>Sem agenda aberta.</td></tr>";
            }
        ?>
        <tr>
            <td coll="3"></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
<?php



?>