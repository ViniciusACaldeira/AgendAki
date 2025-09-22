<link rel="stylesheet" href="/assets/styles/tabela.css">

<a href="dashboard" class="btn-voltar">Voltar</a>

<h1>Agendamento</h1>
<a href="agendamento/cadastro" class="btn-sub">Cadastrar Agendamento</a>


<form id="filtro_agendamento">
    <div class="field">
        <label for="data">Data</label>
        <input type="date" name="data" id="data"/>
    </div>

    <div class="field">
        <label for="funcionarios">Funcionários</label>
        <select name="funcionarios[]" id="funcionarios">
            <option value="">Nenhum</option>
        </select>
    </div>

    <div class="field">
        <label for="servicos">Serviços</label>
        <select name="servicos[]" id="servicos">
            <option value="">Nenhum</option>
        </select>
    </div>
    
    <div class="field">
        <label for="clientes">Clientes</label>
        <select name="clientes[]" id="clientes" multiple>
            <option value="">Nenhum</option>
        </select>
    </div>

    <button type="submit">Consultar</button>
</form>

<table id="tabela_agendamento"></table>

<script src="/assets/script/util.js"></script>
<script src="/assets/script/tabela.js"></script>
<script>
    window.addEventListener( "DOMContentLoaded", ( ) => {
        coletaServico( );
        coletaFuncionario( );
        coletaCliente( );

        montarTabela( );
    });

    function coletaFuncionario( )
    {
        fetch( BASE_URL + "/api/funcionario", {
            method: 'get'
        })
        .then( response => response.json( ) )
        .then( retorno => {
            const funcionario = retorno['data'];
            const select = document.getElementById( "funcionarios" );

            funcionario.forEach( f => {
                const option = document.createElement( "option" );
                option.value = f.id;
                option.textContent = f.nome;

                select.appendChild( option );
            });
        })
        .catch( (error) => {
            console.error( "Falha ao coletar Funcionario: ", error );
        });
    }

    function coletaServico( )
    {
        fetch( BASE_URL + "/api/servico", {
            method: 'get'
        })
        .then( response => response.json( ) )
        .then( retorno => {
            const servico = retorno['data'];
            const select = document.getElementById( "servicos" );

            servico.forEach( f => {
                const option = document.createElement( "option" );
                option.value = f.id;
                option.textContent = f.nome;

                select.appendChild( option );
            });
        })
        .catch( (error) => {
            console.error( "Falha ao coletar Serviços: ", error );
        });
    }

    function coletaCliente( )
    {
        fetch( BASE_URL + "/api/cliente", {
            method: 'get'
        })
        .then( response => response.json( ) )
        .then( retorno => {
            const cliente = retorno['data'];
            const select = document.getElementById( "clientes" );

            cliente.forEach( f => {
                const option = document.createElement( "option" );
                option.value = f.id;
                option.textContent = f.nome;
            
                select.appendChild( option );
            });
        })
        .catch( (error) => {
            console.error( "Falha ao coletar Cliente: ", error );
        });
    }

    function montarTabela( )
    {
        let tabela = new Tabela( "tabela_agendamento" );

        tabela.addCampo( "Data", "data", "", (data) => { return formataData(data)} );
        tabela.addCampo( "Horário", "horario", "" );
        tabela.addCampo( "Serviço", "nome_servico", "" );
        tabela.addCampo( "Funcionario", "nome_funcionario", "" );
        tabela.addCampo( "Cliente", "nome_cliente", "" );
        tabela.setURL( BASE_URL + "/api/agendamento" );
        tabela.setPaginado( true );
        tabela.setFiltro( "filtro_agendamento" );

        tabela.render( );
    }
</script>