<link rel="stylesheet" href="/assets/styles/toast.css">
<link rel="stylesheet" href="/assets/styles/cadastro.css">

<div id="toast-container"></div>

<a href="/agendamento">Voltar</a>

<section>
    <h1>Cadastro</h1>
    <form id="form_cadastro_agendamento" method="POST">
        <div class="field">
            <label for="agenda_id">Selecione a agenda:</label>
            <select name="agenda_id" id="agenda_id"></select>
        </div>
        
        <div class="field">
            <label for="usuario_id">Selecione o usuário: </label>
            <select name="usuario_id" id="usuario_id"></select>
        </div>

        <div class="field-group row-group">
            <div id="servicos" class="field">
                <label for="agenda_servico">Selecione o serviço:</label>
                <select name="agenda_servico" id="agenda_servico"></select>
            </div>
            <div id="horarios" class="field">
                <label for="intervalo">Selecione o horário:</label>
                <select id="intervalo"></select>
            </div>
        </div>

        <div class="field">
            <label for="inicio">Informe o horário de início:</label>
            <input type="text" data-type="time" name="inicio" id="inicio">
        </div>
        
        <button type="submit">Agendar</button>
    </form>
</section>

<script src="/assets/script/util.js"></script>
<script src="/assets/script/toast.js"></script>
<script src="/assets/script/modal.js"></script>
<script src="/assets/script/mascara.js"></script>
<script src="/assets/script/validador.js"></script>
<script>
    document.getElementById( "agenda_id" ).addEventListener( "change", getAgendaServico );
    document.querySelector( "form" ).addEventListener( "submit", cadastrar );
    document.getElementById( "intervalo" ).addEventListener( "change", ( ) => {ajustaLimiteInput( )} );
    document.getElementById( "agenda_servico" ).addEventListener( "change", ( ) => {montarHorarioDisponivel( )});

    window.addEventListener('DOMContentLoaded', () => {
        coletarAgendas( );
        coletarUsuarios( );
    });

    function cadastrar( e )
    {
        e.preventDefault( );

        const formData = new FormData( e.target );

        fetch( BASE_URL + '/api/agendamento/cadastrar', {
                method: 'post',
                body: formData
            }
         )
        .then( response => response.json( ) )
        .then( response => {
            const data = response['data'];
            const status = response['status'];
            const erros = data['erros'];

            if( erros !== undefined )
                erros.forEach( e => mostrarToast(e, TOAST_ERRO) );
            else if( status == 200 )
                mostrarModal( );
        })
        .catch( (error) => console.error(error) );
    }

    function coletarAgendas( )
    {
        fetch( BASE_URL + "/api/agenda" )
        .then( response => response.json( ) )
        .then( response => {
            const data = response['data'];
            const status = response['status'];
            const erros = data['erros'];

            if( erros !== undefined )
                erros.forEach( e => mostrarToast(e, TOAST_ERRO) );
            else if( status == 200 )
            {
                const select = document.getElementById( "agenda_id" );
                select.innerHTML = "";

                data.forEach( a => {
                    const option = document.createElement( "option" );
                    option.value = a['id'];
                    option.textContent = `${formataData(a['data'])} - ${a['nome']}`;

                    select.appendChild( option );
                });

                select.dispatchEvent( new Event('change') );
            }
        })
        .catch( (error) => console.error( error ) );
    }

    function coletarUsuarios( )
    {
        fetch( BASE_URL + "/api/cliente" )
        .then( response => response.json( ) )
        .then( response => {
            const data = response['data'];
            const status = response['status'];
            const erros = data['erros'];

            if( erros !== undefined )
                erros.forEach( e => mostrarToast(e, TOAST_ERRO) );
            else if( status == 200 )
            {
                const select = document.getElementById( "usuario_id" );
                select.innerHTML = "";

                data.forEach( usuario => {
                    const option = document.createElement( "option" );
                    option.value = usuario.id;
                    option.textContent = `${usuario['nome']} - ${formatarTelefone( usuario['telefone'] )}`;

                    select.appendChild( option );
                });
            }
        })
        .catch( (error) => console.error( error ) );
    }

    function getAgendaServico( event )
    {
        const agenda_id = event.target.value;

        fetch(BASE_URL + `/api/agenda/servico?id=${agenda_id}`)
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
        const select = document.getElementById( "agenda_servico" );
        select.innerHTML = "";

        servicos.forEach((servico, index) => {
            const option = document.createElement('option');
            option.value = servico['agenda_servico_id'];
            option.innerText = servico['nome'];

            select.appendChild( option );
        });
    }

    function montarHorarioDisponivel( )
    {
        const servico = document.getElementById("agenda_servico").value;
        const select = document.getElementById('intervalo');
        select.innerHTML = "";

        fetch( BASE_URL + `/api/agendamento/servico/disponivel?id=${servico}` )
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
        if( option == null )
            return;

        const input = document.getElementById( "inicio" ); 
        input.min = option.dataset.min;
        input.max = option.dataset.max;
    }

    function limpaForm( )
    {
        document.querySelector( "#inicio" ).value = "";

        montarHorarioDisponivel( );
    }

    function mostrarModal( )
    {
        modal_abrir( { titulo: "Cadastrar outro?", botoes: [ {texto: "Voltar a tela de início", acao: (modal) => { redireciona( "/agenda" ) } }, {texto: "Cadastrar outro", acao: (modal) => { limpaForm(); modal_fechar( ) }}]} )
    }
</script>