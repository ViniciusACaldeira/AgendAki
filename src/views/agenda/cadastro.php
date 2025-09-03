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

        <section id="configuracao">
            <h2>Configuração Agenda</h2>

            <div class="field">

                <div class="field-group row-group">
                    <div class="field">
                        <label for="tipo">Selecione o tipo de agenda</label>
                        <select name="tipo" id="tipo">
                            
                        </select>
                    </div>

                    <div class="field">
                        <label for="tamanho" data-tooltip="Para SLOT será o tamanho de cada grupo, por exemplo 00:30, cada grupo será composto por 30 minutos, 08:00, 08:30, 09:00...
                        Para DIFERENCA_LIMITADA será a diferença máxima livre que pode ter entre um serviço e outro, se um serviço que demora 30 minutos começou as 08:00 o próximo pode apenas selecionar das 08:30 a 08:40">
                        Tamanho da agenda</label>
                        <input type="text" data-type="time" name="tamanho" id="tamanho">
                    </div>
                </div>
                
                <div class="field-group row-group">
                    <div class="field checkbox-field">
                        <input type="checkbox" name="fila_espera" id="fila_espera">
                        <label for="fila_espera">Fila de espera</label>
                    </div>

                    <div class="field">
                        <label for="quantidade_fila">Quantidade na fila</label>
                        <input type="number" name="quantidade_fila" id="quantidade_fila" min="0">
                    </div>
                </div>
            </div>
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
        coletarTipoAgenda( );
    });

    function coletarTipoAgenda( )
    {
        const select = document.getElementById( "tipo" );

        fetch( BASE_URL + "/api/agenda/tipo" )
        .then( response => response.json( ) )
        .then( response => {
            const data = response['data'];
            const erros = response['erros'];
            const status = response['status'];

            if( erros != undefined )
                erros.forEach( e => mostrarToast( e, TOAST_ERRO ) );

            if( status == 200 )
            {
                data.forEach( t => {
                    const option = document.createElement( "option" );
                    option.value = t.id;
                    option.textContent = t.nome;

                    select.appendChild( option );
                });
            }
        })
    }

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

        document.getElementById( "inicio" ).dispatchEvent( new Event("change") );
        document.getElementById( "fim" ).dispatchEvent( new Event("change") );
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
    }

    function mostrarModal( )
    {
        modal_abrir( { titulo: "Cadastrar outro?", botoes: [ {texto: "Voltar a tela de início", acao: (modal) => { redireciona( "/agenda" ) } }, {texto: "Cadastrar outro", acao: (modal) => { limpaForm(); modal_fechar( ) }}]} )
    }
</script>