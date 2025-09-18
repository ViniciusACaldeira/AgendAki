<style>

    #horarios button {
        background: #ecf0f1;
        color: #2c3e50;
        border: 1px solid #bdc3c7;
        border-radius: 6px;
        padding: 8px 14px;
        cursor: pointer;
        transition: all 0.3s;
    }

    #horarios button:hover {
        background: #dfe6e9;
    }

    #horarios button.horarioSelecionado {
        background: #3498db;
        color: #fff;
        border-color: #2980b9;
        font-weight: bold;
        transform: scale(1.05);
    }

    form {
      max-width: 700px;
      margin: 0 auto;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    h4 {
      margin-top: 0;
      font-size: 20px;
      color: #2c3e50;
      border-left: 4px solid #3498db;
      padding-left: 10px;
    }

    .etapa {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .escondido {
      display: none;
    }

    button {
      padding: 10px 18px;
      border: none;
      border-radius: 6px;
      background: #3498db;
      color: #fff;
      font-size: 14px;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #2980b9;
    }

    button[data-escolha] {
      background: #ecf0f1;
      color: #2c3e50;
      border: 1px solid #bdc3c7;
    }

    button[data-escolha]:hover {
      background: #dfe6e9;
    }

    /* Área de opções */
    #escolha {
      gap: 20px;
    }

    #escolha > button {
      margin-right: 10px;
    }

    /* Seção de horário */
    #horario_tipo {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }

    #horario_tipo label {
      margin-left: 5px;
      font-weight: 500;
      color: #34495e;
    }

    #horarios {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 10px;
    }

    #horarios button {
      background: #ecf0f1;
      color: #2c3e50;
      border: 1px solid #bdc3c7;
    }

    #horarios button:hover {
      background: #dfe6e9;
    }

    /* Seção de confirmação */
    [data-etapa="3"] span {
      display: block;
      margin-bottom: 6px;
      font-size: 15px;
      color: #2c3e50;
    }

    [data-etapa="3"] button {
      margin-right: 10px;
    }
</style>
<link rel="stylesheet" href="/assets/styles/calendario.css">

<section id="proximo_agendamento" class="card-list-section">
    <h1>Próximo agendamento</h1>

    <div class="card-list-horizontal" id="agendamentos">
        <div id="sem_agendamento" class="card empty" style="display: none;">
            <div class="body">
                <span>📅 Você ainda não possui agendamentos</span>
                <button class="primary">Agendar agora</button>
            </div>
        </div>
    </div>
</section>

<section id="novo_agendamento">
    <h1>Agendar novo agendamento</h1>
    
    <form>
        <input type="hidden" id="agenda_servico" name="agenda_servico">

        <section id="servico" class="etapa" data-etapa="1">
            <h4>Escolha o Serviço</h4>
            <div id="select_servico"></div>

            <button type="button" data-etapa="2">Próximo</button>
        </section>

        <section id="escolha" class="etapa escondido" data-etapa="2">
            <h4>Escolha</h4>

            <button type="button" data-escolha="1">Por Funcionário</button>
            <button type="button" data-escolha="2">Por Data</button>

            <div>
                <button type="button" data-etapa="1">Voltar</button>
            </div>
        </section>

        <section data-escolha="1" class="etapa escondido" data-etapa="4">
            <h4>Escolha o Funcionário</h4>
            
            <div id="select_funcionario"></div>

            <div class="voltar">
                <button type="button" data-etapa="1">Voltar</button>
            </div>
        </section>

        <section data-escolha="2" id="etapa_data" class="etapa escondido" data-etapa="5">
            <h4>Escolha a Data</h4>

            <div class="calendario" id="calendario"></div>

            <div class="voltar">
                <button type="button" data-etapa="1">Voltar</button>
            </div>
        </section>
            
        <section class="etapa escondido" data-etapa="6">
            <h4>Escolha o Horário</h4>

            <section id="horario">
                <div id="horario_tipo">
                    <div>
                        <input type="radio" name="horario_tipo" id="horario_tipo_qualquer" value="qualquer" checked>
                        <label for="horario_tipo_qualquer">Qualquer</label>
                    </div>
                    <div>
                        <input type="radio" name="horario_tipo" id="horario_tipo_manha" value="manha">
                        <label for="horario_tipo_manha">Manhã</label>
                    </div>
                    <div>
                    <input type="radio" name="horario_tipo" id="horario_tipo_tarde" value="tarde">
                        <label for="horario_tipo_tarde">Tarde</label>
                    </div>
                    <div>
                        <input type="radio" name="horario_tipo" id="horario_tipo_noite" value="noite">
                        <label for="horario_tipo_noite">Noite</label>
                    </div>
                </div>

                <div id="horarios"></div>
                <input type="hidden" name="inicio" id="inicio">
            </section>

            <div>
                <button type="button" data-etapa="5">Voltar</button>
            </div>
        </section>

        <section class="etapa escondido" data-etapa="7">
            <h4>Confirmar</h4>

            <span>Serviço: <span id="form_servico"></span></span>
            <span>Data: <span id="form_data"></span></span>
            <span>Horário: <span id="form_horario"></span></span>
            <span>Funcionário: <span id="form_funcionario"></span></span>
            <span>Valor Final: <span id="form_valor"></span></span>
            
            <button type="button" data-etapa="6">Voltar</button>
            <button type="submit">Agendar</button>
        </section>
    </form>
</section>

<script src="/assets/script/calendario.js"></script>
<script>
    const etapas = [];
    const escolhas = [];
    const selects = [];
    let calendario = null;


    window.addEventListener( "DOMContentLoaded", (e) => {
        coletaEtapas( );
        coletarEscolhas( );

        calendario = new Calendario( "calendario", false );
        calendario.setDataMinima( new Date( ) );
        calendario.addAcoesAoSelecionar( [coletarAgenda] );
        calendario.render( );

        document.querySelector( "form" ).addEventListener( "submit", (e) => cadastrar( e ) );

        const agendamento = document.getElementById( "novo_agendamento" );
        const botoes = agendamento.querySelectorAll( "button[data-etapa]" );

        botoes.forEach( b => b.addEventListener( "click", (e) => alterarEtapa( e.target.dataset.etapa ) ) );

        coletarServico( );

        //Ultimos agendamentos
        coletarAgendamento( );

        document.querySelectorAll( "[name='horario_tipo']").forEach( b => b.addEventListener( "click", (e) => { onChangeTipoHorario(e.target.value) } ) );
    });

    function coletarFuncionarioAgenda( data, servico )
    {
        getAPI( `/api/agenda?data=${data}&servico=${servico}`, montarFuncionario, "Falha ao coletar a agenda do funcionário." );
    }

    function coletarData( )
    {
        const servico = coletarSelect( "servico" ).getSelecionado( )[0].valor;
        getAPI( `/api/agenda?servico=${servico}`, (data) => { montarCalendario(data, (data) => { coletarFuncionarioAgenda( data, servico ); setAgendaServico( data ) } ) }, "Falha ao coletar a agenda." );
    }

    function coletarAgendaFuncionario( select )
    {
        const funcionario = select.getSelecionado( )[0].valor;
        const servico = coletarSelect( "servico" ).getSelecionado( )[0].valor ?? 0;
        let etapa_atual = Number(select.getElemento( ).closest( "[data-etapa]" ).dataset.etapa);

        getAPI( `/api/agenda?funcionario=${funcionario}&servico=${servico}`, (data) => { montarCalendario(data, (data) => {setAgendaServico( data )} )}, "Falha ao coletar os da agenda funcionários." );

        if( etapa_atual == 4 )//começa por ele
            calendario.limpar( );

        alterarEtapa( ++etapa_atual );
    }

    function montarFuncionario( data )
    {
        const opcoes = data.map( f => {return {"texto": f.nome, "valor": f.id } } );
        montarSelectFuncionario( opcoes, () => { alterarEtapa( 6 ) });
        alterarEtapa( 5 );
    }

    function coletarAgenda( )
    {

    }

    function montarCalendario( data, acao )
    {
        calendario.setAcoesAoSelecionar( [acao] );
        const diasValidos = [];
        data.forEach( d => diasValidos.push( d.data ) );
        calendario.setDiasValidos( diasValidos );
        calendario.setValidaDiasValidosVazio( true );
        calendario.render( );

        coletarAgendaServico( data )
    }

    let agendaServico = [];

    function coletarAgendaServico( data )
    {
        agendaServico = [];

        data.forEach( d => {
            agendaServico.push( { id: d.agenda_servico_id, data: d.data } );
        });
    }

    function setAgendaServico( data )
    {
        const aServico = agendaServico.filter( sa => sa.data == data );

        if( aServico.length > 0 )
        {
            const id = aServico[0].id;

            document.getElementById( "agenda_servico" ).value = id;
            coletarHorariosDisponiveis( id );

            alterarEtapa( 6 );
        }
    }

    function coletarHorariosDisponiveis( id )
    {
        getAPI( `/api/agendamento/servico/disponivel?id=${id}`, montarHorariosDisponiveis, "Falha ao consultar horários disponíveis" );
    }

    let horariosDisponiveis = [];
    let horariosTipo = 0;

    function montarHorariosDisponiveis( data )
    {
        horariosDisponiveis = [];
        horariosDisponiveis = data.horarios;
        horariosTipo = data.tipo;

        onChangeTipoHorario( "qualquer" );
    }

    function onChangeTipoHorario( tipo )
    {
        let min = "00:00";
        let max = "23:59";

        switch( tipo )
        {
            case "manha":
                min = "00:00";
                max = "11:59";
                break;
            case "tarde":
                min = "12:00";
                max = "16:59";
                break;
            case "noite":
                min = "17:00";
                max = "23:59";
                break;
            case "qualquer":
            default:
                break;
        }

        const horarioInicio = horariosDisponiveis.filter( h => h.disponivel ).map( h => { 
            if( horariosTipo == 3 )
                return h.hora;
            else
                return h.inicio; 
        });

        const horarios = filtrarHorarios( horarioInicio, min, max );
        
        montarHorarios( horarios );
    }

    function montarHorarios( horarios )
    {
        eHorarios = [];

        const divH = document.getElementById( "horarios" );
        divH.innerHTML = "";

        const horarioSelecionado = document.getElementById( "inicio" ).value;
        const temHorario = horarios.filter( h => h == horarioSelecionado );
        
        if( horarioSelecionado != "" && temHorario.length == 0 )
            divH.appendChild( criarBotaoHorario( horarioSelecionado, true ) );

        horarios.forEach( h => divH.appendChild( criarBotaoHorario( h, h == horarioSelecionado ) ) );
    }

    function criarBotaoHorario( h, selecionado = false )
    {
        const div = document.createElement( "div" );
        const button = document.createElement( "button" );
        button.type = "button";
        button.value = h;
        button.textContent = `Horário das ${h}`;
        button.addEventListener( "click", alterarHorarioEscolhido );

        if( selecionado )
            button.classList.add( "horarioSelecionado" );

        div.appendChild( button );

        return div;
    }

    let eHorarios = [];

    function alterarHorarioEscolhido( e )
    {
        if( e.target.classList.contains( "horarioSelecionado") )
        {
            e.target.classList.remove( "horarioSelecionado" );
            document.getElementById( "inicio" ).value = "";
        }
        else
        {
            const horarioSelecionado = document.querySelector( ".horarioSelecionado" );

            if( horarioSelecionado != null )
                horarioSelecionado.classList.remove( "horarioSelecionado" );

            e.target.classList.add( "horarioSelecionado" );
            document.getElementById( "inicio" ).value = e.target.value;

            alterarEtapa( 7 );
        }
    }

    function alterarEscolha( e )
    {
        const escolha = e.target.dataset.escolha;

        escolhas.forEach( es => {
            const voltar = es.elemento.closest( "section" ).querySelector( ".voltar" );

            if( es.id == escolha )
            {
                es.elemento.dataset.etapa = 4;
                voltar.querySelector( "button" ).dataset.etapa = 2;
            }
            else
            {
                es.elemento.dataset.etapa = 5;
                voltar.querySelector( "button" ).dataset.etapa = 4;
            }

        } );

        switch( escolha )
        {
            case "1":
            default:
                coletarFuncionario( );
                break;
            case "2":
                coletarData( );
                break;
        }

        resetaEscolhas( );
        alterarEtapa( 4 );
    }

    function resetaEscolhas( )
    {
        calendario.limpar( );
        const funcionario = coletarSelect( "funcionario" );

        if( funcionario != null )
            funcionario.limpar( );

        etapas.length = 0;
        coletaEtapas( );
    }

    function coletaEtapas( )
    {
        const etapas_section = document.querySelectorAll( "section[data-etapa]" );

        etapas_section.forEach( e => {
            const etapa = e.dataset.etapa;
            etapas.push( {"id": etapa, "elemento": e } );
        });
    }

    function coletarEscolhas( )
    {
        const botoes = document.querySelectorAll('button[data-escolha]');
        botoes.forEach( e => {
            const escolha = e.dataset.escolha;
            e.addEventListener( "click", alterarEscolha );
            escolhas.push( {"id": escolha, "elemento": document.querySelector(`section[data-escolha="${escolha}"]`)} );
        });
    }

    function alterarEtapa( etapa )
    {
        if( etapa == 7 )
            ajustaEtapaConfirmacao( );

        etapas.forEach( e => {
            if( e.id != etapa )
                e.elemento.classList.add( "escondido" );
            else
                e.elemento.classList.remove( "escondido" );
        });
    }

    function alterarVisibilidade( e, visivel )
    {
        if( visivel )
            e.classList.remove( "escondido" )
        else
            e.classList.add( "escondido" );
    }

    function ajustaEtapaConfirmacao( )
    {
        const servico = document.getElementById( "form_servico" );
        const select_servico = selects.filter( s => s.tipo == "servico" )[0].elemento;
        servico.textContent = select_servico.getSelecionado( )[0] ? select_servico.getSelecionado( )[0].texto : "Não selecionado";

        const funcionario = document.getElementById( "form_funcionario" );
        const select_funcionario = selects.filter( s => s.tipo == "funcionario" )[0].elemento;
        funcionario.textContent = select_funcionario.getSelecionado( )[0] ? select_funcionario.getSelecionado( )[0].texto : "Não selecionado";

        const dataSelecionada = Array.from( calendario.coletarDiasSelecionados( ) )[0];

        const data = document.getElementById( "form_data" );
        data.textContent = formataData( dataSelecionada );

        const horario = document.getElementById( "form_horario" );
        horario.textContent = document.getElementById( "inicio" ).value;
    }

    function cadastrar( e )
    {
        e.preventDefault( );

        const formData = new FormData( e.target );

        postAPI( `/api/agendamento/cadastrar`, formData, () => { mostrarToast( "Agendamento realizado com sucesso.", TOAST_SUCESSO ); coletarAgendamento( ); alterarEtapa(1); }, "Falha ao cadastrar o agendamento." );
    }   
    
    function coletarFuncionario( )
    {
        const servico = coletarSelect( "servico" ).getSelecionado( )[0].valor;
        getAPI( `/api/funcionario?servico=${servico}`, montarOpcoesFuncionario, "Falha ao consultar os funcionários." );
    }

    function montarOpcoesFuncionario( data )
    {
        const opcoes = data.map( f => {return {"texto": f.nome, "valor": f.id } } );
        montarSelectFuncionario( opcoes, coletarAgendaFuncionario );
    }

    function montarSelectFuncionario( opcoes, acao )
    {
        let select = coletarSelect( "funcionario" );

        if( select == null )
        {
            select = new Select( "select_funcionario", "Selecione o funcionário", opcoes );
            selects.push( {tipo: "funcionario", elemento: select } );
        }
        else
            select.setOpcoes( opcoes );

        select.setAcao( acao );
        select.build( );
    }

    function coletarServico( )
    {
        getAPI( "/api/servico", montarSelectServico, "Falha ao consultar os serviços" );
    }

    function montarSelectServico( data )
    {
        const opcoes = data.map( e => { return {"texto": e.nome, "valor": e.id} } );

        const select = new Select( "select_servico", "Selecione o serviço", opcoes );
        select.build( );
        selects.push( {tipo: "servico", elemento: select } );
    }

    function coletarAgendamento( )
    {
        fetch( BASE_URL + "/api/agendamento?apartir=" + dateToString( new Date( ) ) )
        .then( response => response.json( ) )
        .then( response => {
            const data = response['data'];
            const status = response['status'];

            if( status == 200 )
            {
                if( data.length == 0 )
                    document.getElementById( "sem_agendamento" ).style.display = "block";
                else
                {
                    const lista = document.getElementById( "agendamentos" );
                    lista.innerHTML = "";
                    data.forEach( a => lista.appendChild( montarAgendamento( a ) ) );
                }
            }
            else
                mostrarToast( "Falhao ao consultar agendamentos.", TOAST_ERRO );
        })
        .catch( (error) => console.error(error) );
    }

    function coletarSelect( tipo )
    {
        const select = selects.filter( s => s.tipo == tipo );

        if( select.length > 0 )
            return select[0].elemento;

        return null;
    }

    function montarAgendamento( agendamento )
    {
        const card = document.createElement("div");
        card.classList.add("card");

        card.innerHTML = `
            <div class="header">
                ${formataData(agendamento.data)} - ${agendamento.horario}
            </div>
            <div class="body">
                <span>Serviço: ${agendamento.nome_servico}</span>
                <span>Valor: ${formatarPreco( agendamento.valor )}</span>
                <span>Funcionário: ${agendamento.nome_funcionario}</span>
            </div>
            <div class="footer">
                <button class="primary">Confirmar</button>
                <button class="danger">Cancelar</button>
            </div>
        `;

        return card;
    }

</script>