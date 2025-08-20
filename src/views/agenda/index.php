<link rel="stylesheet" href="/assets/styles/tabela.css">
<a href="/dashboard">Voltar</a>

<h1>Agenda</h1>
<a href="/agenda/cadastro">Cadastrar Agenda</a>
<form id="filtro_agenda" action="get">
    <div class="field">
        <label for="data">Data</label>
        <input type="date" name="data" id="data">
    </div>
    <div class="field">
        <label for="funcionario">Funcionário</label>
        <select name="funcionario" id="funcionario">
            <option value="">----</option>
        </select>
    </div>
        
    <button type="submit">Consultar</button>
</form>
<table id="tabela_agenda"></table>

<script src="/assets/script/util.js"></script>
<script src="/assets/script/tabela.js"></script>
<script>
    window.addEventListener('DOMContentLoaded', () => { 
        coletaFuncionarios( );
        montaTabela( );
    });

    function coletaFuncionarios( )
    {
        fetch( "http://localhost:8000/api/funcionario" )
        .then( response => response.json( ) )
        .then( retorno => {
            const data = retorno['data'];
            const status = retorno['status'];

            if( status != 200 )
                mostrarToast( "Falha ao coletar os funcionários.", TOAST_ERRO );
            else
            {
                const select = document.getElementById( "funcionario" );
                
                data.forEach( (funcionario) => {
                    const option = document.createElement( "option" );
                    option.value = funcionario.id;
                    option.textContent = funcionario.nome;
                    
                    select.appendChild( option );
                });
            }
        })
        .catch( (error) => {console.log( error ) } );
    }

    function montaTabela( )
    {
        let tabela = new Tabela( "tabela_agenda" );

        tabela.addCampo( "Funcionario", "nome" );
        tabela.addCampo( "Data", "data", "", (data) => {return formataData(data)} );
        tabela.addCampo( "Início", "inicio", "" );
        tabela.addCampo( "Fim", "fim", "" );
        tabela.setURL( "http://localhost:8000/api/agenda" );
        tabela.setPaginado( true );
        tabela.setFiltro( "filtro_agenda" );

        tabela.render( );
    }
</script>