<?php
    use Vennizlab\Agendaki\helpers\Flash;
    use Vennizlab\Agendaki\models\FuncionarioModel;

    Flash::print( ); 
?>

<a href="/agenda">Voltar</a>

<form action="/agenda/cadastrar" method="POST">
    <label for="funcionario_id_agenda">Funcionário</label>
    <select name="funcionario_id_agenda" id="funcionario_id_agenda" onchange="onChangeFuncionario(this)">
        <?php
            $funcionarioModel = new FuncionarioModel( );
            $funcionarios = $funcionarioModel->getAll();

            foreach( $funcionarios as $funcionario )
                echo "<option value='".$funcionario['id']."'>".$funcionario['nome']."</option>";
        ?>
    </select>

    <label for="data_agenda">Data:</label>
    <input type="date" id="data_agenda" name="data_agenda">

    <label for="inicio_agenda">Início:</label>
    <input type="time" name="inicio_agenda" id="inicio_agenda">

    <label for="fim_agenda">Fim:</label>
    <input type="time" name="fim_agenda" id="fim_agenda">

    <section id="servicos">

    </section>

    <button type="submit">Cadastrar</button>
</form>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        document.getElementById('funcionario_id_agenda').dispatchEvent(new Event('change'));
    });

    function onChangeFuncionario( event )
    {
        let funcionario = event.value;
        document.getElementById("servicos").innerText = "";

        fetch(`http://localhost:8000/api/servico/funcionario?id=${funcionario}`)
        .then( response => response.json() )
        .then( data => {
            montaServicos( data['data'] );
        })
        .catch( error => {
            console.error( "Erro na requisição: ", error );
        });
    }

    function montaServicos( servicos )
    {
        section = document.getElementById("servicos");

        console.log(servicos);

        servicos.forEach((servico, index) => {

            const label = document.createElement('label');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = 'servico[]';
            checkbox.value = servico['id'];
            checkbox.id = 'servico_' + servico['id'];

            label.htmlFor = checkbox.id;
            label.appendChild(checkbox);
            label.appendChild(document.createTextNode(' ' + servico['nome']));
            section.appendChild(label);
            section.appendChild(document.createElement('br'));
        });
        
    }
</script>