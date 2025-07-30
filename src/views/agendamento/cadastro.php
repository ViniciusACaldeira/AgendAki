<?php
    use Vennizlab\Agendaki\helpers\Flash;

    Flash::print( ); 
?>

<a href="/agendamento">Voltar</a>

<form action="cadastrar" method="POST">
    <label for="agenda_id">Selecione a agenda: </label>
    <select name="agenda_id" id="agenda_id" onChange="getAgendaServico(this)">
        <?php
            use Vennizlab\Agendaki\models\AgendaModel;
            use Vennizlab\Agendaki\models\UsuarioModel;

            $agendaModel = new AgendaModel( );

            $agendas = $agendaModel->getApartirDe( date('Y-m-d') );

            foreach( $agendas as $agenda )
                echo "<option value='".$agenda['id']."'>". $agenda['data']. " - " . $agenda['nome'] ."</option>";
        ?>
    </select>

    <section id="usuarios">
        <label for="usuario_id">Selecione o usuário: </label>
        <select name="usuario_id" id="usuario_id">
            <?php
                $usuarioModel = new UsuarioModel();
                $clientes = $usuarioModel->getAllCliente( );

                foreach ($clientes as $cliente): 
            ?>
                    <?php $nome = htmlspecialchars($cliente['nome']);?>
                    <option id="usuario_<?= $cliente['id']?>" value="<?= $cliente['id'] ?>" ><?= $nome ?> - <?= $cliente['telefone']?></option>
                <?php endforeach;?>
        </select>
    </section>

    <section id="servicos">

    </section>

    <section id="horarios">
        
    </section>

    <label for="inicio">Informe o horário de início</label>
    <input type="time" name="inicio" id="inicio">

    
    <button type="submit">Agendar</button>
</form>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        document.getElementById('agenda_id').dispatchEvent(new Event('change'));
    });

    function getAgendaServico( event )
    {
        const agenda_id = event.value;

        document.getElementById("servicos").innerText = "";

        fetch(`http://localhost:8000/api/agenda/servico?id=${agenda_id}`)
        .then( response => response.json() )
        .then( data => {
            montaServicos( data['data'] );
        })
        .finally( () => montarHorarioDisponivel() )
        .catch( error => {
            console.error( "Erro na requisição: ", error );
        });
    }

    function montaServicos( servicos )
    {
        section = document.getElementById("servicos");

        const label = document.createElement('label');
        const select = document.createElement('select');
        select.name = "servico_id";
        select.id = "servico_id";
        select.onchange = () => montarHorarioDisponivel( );

        label.innerText = "Selecione o serviço: ";
        label.htmlFor = select.id;

        servicos.forEach((servico, index) => {
            const option = document.createElement('option');
            option.value = servico['agenda_servico_id'];
            option.innerText = servico['nome'];

            select.appendChild( option );
        });

        label.appendChild(select);
        section.appendChild(label);
    }

    function montarHorarioDisponivel( )
    {
        servico = document.getElementById("servico_id").value;

        section = document.getElementById("horarios");
        section.innerText = "";

        const label  = document.createElement('label');
        const select = document.createElement('select');
        select.id = "intervalo";
        select.name = "intervalo";
        select.onchange = () => ajustaLimiteInput( );

        label.htmlFor = select.id;
        label.innerText = "Selecione um período: ";

        label.appendChild( select );
        section.appendChild( label );

        fetch( `http://localhost:8000/api/agendamento/servico/disponivel?id=${servico}` )
        .then( response => response.json() )
        .then( data => {
            data['data'].forEach( (intervalo, index) => {
                const option = document.createElement("option");
                option.value = index;
                option.dataset.min = intervalo['inicio'];
                option.dataset.max = intervalo['fim'];
                option.innerText = `Entre ${intervalo['inicio']} e ${intervalo['fim']}`;
                option.id = `intervalo_${index}`;

                select.appendChild( option );
            });
        })
        .finally( () => ajustaLimiteInput() )
        .catch( error => {
            console.error( "Erro na requisição: ", error );
        });
    }

    function ajustaLimiteInput( )
    {
        const option = document.getElementById( `intervalo_${document.getElementById("intervalo").value}`);
        const input = document.getElementById( "inicio" ); 
        input.min = option.dataset.min;
        input.max = option.dataset.max;
    }

</script>