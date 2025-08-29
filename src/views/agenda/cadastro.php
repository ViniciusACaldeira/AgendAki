<link rel="stylesheet" href="/assets/styles/cadastro.css">
<link rel="stylesheet" href="/assets/styles/agenda/cadastro.css">
<link rel="stylesheet" href="/assets/styles/calendario.css">

<a href="/agenda">Voltar</a>

<section>
    <h1>Cadastro</h1>

    <form id="form_cadastro_agenda" method="POST">
        <label for="funcionario_id">Funcionário</label>
        <select name="funcionario_id" id="funcionario_id"></select>

        <div class="field">
            <div class="calendario" id="calendario"></div>
        </div>
        
        <div class="field-group row-group">
            <div class="field">
                <label for="inicio">Início:</label>
                <input type="text" data-type="time" name="inicio" id="inicio">
            </div>
            <div class="field">
                <label for="fim">Fim:</label>
                <input type="text" data-type="time" name="fim" id="fim">
            </div>
        </div>

        <section id="servicos" hidden>
            <h4>Serviços</h4>
        </section>

        <button type="submit">Cadastrar</button>
    </form>
</section>


<script src="/assets/script/modal.js"></script>
<script src="/assets/script/calendario.js"></script>
<script>
    const calendario = new Calendario( "calendario" );
    calendario.render( );

    document.getElementById( "funcionario_id" ).addEventListener( "change", onChangeFuncionario );
    document.getElementById( "inicio" ).addEventListener( "change", (e) => ajustaTempo( e.target.value, true ) );
    document.getElementById( "fim" ).addEventListener( "change", (e) => ajustaTempo( e.target.value, false ) );

    window.addEventListener('DOMContentLoaded', () => {
        document.querySelector( 'form' ).addEventListener( "submit", cadastrar );
        coletarFuncionarios( );
    });

    function ajustaTempo( horario, inicio )
    {
        const horarios = document.querySelectorAll( `[id^='servico_${inicio ? "inicio" : "fim"}_']`);
        horarios.forEach( h => {
            h.value = horario;
            completarTempo( h );
        });
    }

    function cadastrar( e )
    {
        e.preventDefault( );
        
        data = calendario.coletarDiasSelecionados( );
        const formData = new FormData( e.target );
        formData.set( "data", Array.from(data) );

        fetch( BASE_URL + "/api/agenda/cadastrar", {
            method: "post",
            body: formData
        })
        .then( response => response.json( ) )
        .then( response => {
            const data = response["data"];
            const status = response['status'];
            const erros = data['erros'];

            if( erros !== undefined )
                erros.forEach( e => mostrarToast( e, TOAST_ERRO ) );
            else if( status == 200 )
                mostrarModal( );
                
        })
        .catch( (error) => console.error(error) );
    }

    function coletarFuncionarios( )
    {
        const select = document.getElementById( "funcionario_id" );

        fetch( BASE_URL + "/api/funcionario" )
        .then( response => response.json( ) )
        .then( response => {
            const data = response['data'];
            const status = response['status'];
            const erros = data['erros'];

            if( erros !== undefined )
                erros.forEach( e => mostrarToast( e, TOAST_ERRO ) );
            else if( status == 200 )
            {
                data.forEach( f => {
                    const option = document.createElement( "option" );
                    option.value = f.id;
                    option.textContent = f.nome;

                    select.appendChild( option );
                });

                select.dispatchEvent(new Event('change'));
            }
        })
        .catch( (error) => { console.error(error) } );
    }

    function onChangeFuncionario( event )
    {
        let funcionario = event.target.value;
        document.getElementById("servicos").innerText = "";

        fetch( BASE_URL + `/api/servico/funcionario?id=${funcionario}`)
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
        section.hidden = false;
        section.innerText = "";
        
        const h4 = document.createElement( "h4" );
        h4.textContent = "Serviços";
        h4.className = "center";
        
        section.appendChild( h4 );

        servicos.forEach((servico, index) => {
            const divServico = document.createElement( "div" );
            divServico.className = "field card";

            const divCheckbox = document.createElement( "div" );
            divCheckbox.className = "field checkbox-field";

            const label = document.createElement('label');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = 'servicos[]';
            checkbox.value = servico['id'];
            checkbox.id = 'servico_' + servico['id'];
            checkbox.checked = true;
            checkbox.addEventListener('change', () => ajustaInputs(servico['id']));

            label.htmlFor = checkbox.id;
            label.textContent = ` ${servico['nome']}`;
            divCheckbox.appendChild( checkbox );
            divCheckbox.appendChild( label );
            divServico.appendChild(divCheckbox);

            const divHorario = document.createElement( "div" );
            divHorario.className = "field-group row-group";
            
            const divInicio = document.createElement( "div" );
            divInicio.className = "field";

            const label_inicio = document.createElement('label');
            const inicio_input = document.createElement('input');
            inicio_input.type = 'text';
            inicio_input.dataset.type = "time";
            inicio_input.name = 'servico_inicio[]';
            inicio_input.id = 'servico_inicio_' + servico['id'];

            label_inicio.htmlFor = inicio_input.id;
            label_inicio.textContent = "Ínicio: ";
            divInicio.appendChild( label_inicio );
            divInicio.appendChild( inicio_input );

            const divFim = document.createElement( "div" );
            divFim.className = "field";

            const label_fim = document.createElement('label');
            const fim_input = document.createElement('input');
            fim_input.type = 'text';
            fim_input.dataset.type = "time";
            fim_input.name = 'servico_fim[]';
            fim_input.id = 'servico_fim_' + servico['id'];

            label_fim.htmlFor = fim_input.id;
            label_fim.textContent = "Fim: ";
            divFim.appendChild(label_fim);
            divFim.appendChild(fim_input);

            divHorario.append( divInicio );
            divHorario.append( divFim );
            divServico.append( divHorario );
            section.appendChild( divServico );
        });
    }
    
    function ajustaInputs( id )
    {
        const checkbox = document.getElementById('servico_' + id);
        const inicio = document.getElementById('servico_inicio_' + id);
        const fim = document.getElementById('servico_fim_' + id);

        const ativo = checkbox.checked;

        inicio.disabled = !ativo;
        fim.disabled = !ativo;
    }

    function limpaForm( )
    {
        document.querySelector( "#data" ).value = "";
    }

    function mostrarModal( )
    {
        modal_abrir( { titulo: "Cadastrar outro?", botoes: [ {texto: "Voltar a tela de início", acao: (modal) => { redireciona( "/agenda" ) } }, {texto: "Cadastrar outro", acao: (modal) => { limpaForm(); modal_fechar( ) }}]} )
    }
</script>