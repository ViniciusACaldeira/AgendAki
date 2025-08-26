<link rel="stylesheet" href="/assets/styles/tabela.css">


<a href="/dashboard">voltar</a>

<h1>Funcionário</h1>
<a href="/funcionario/cadastro">Cadastrar Funcionário</a>

<form id="filtro_funcionarios" method="GET">
    <div class="field">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome">
    </div>
    <div class="field">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email">
    </div>
    <div class="field">
        <label for="telefone">Telefone:</label>
        <input type="text" name="telefone" id="telefone">
    </div>

    <button type="submit">Consultar</button>
</form>
<table id="tabela_funcionarios"></table>

<script src="/assets/script/tabela.js"></script>
<script>
    window.addEventListener('DOMContentLoaded', () => { montaTabela( ) });
    document.getElementById( "telefone" ).addEventListener( "input", (telefone) => {mascaraTelefone(telefone);});

    acoes = [
        (item) => {
            const btnDetalhar = document.createElement( "button" );
            btnDetalhar.textContent = "Detalhar";
            btnDetalhar.onclick = () => detalhar( item.id );

            return btnDetalhar;
        }
    ];

    const formatacaoData = { telefone: desmascararTelefone};

    function detalhar( id )
    {
        window.location = BASE_URL + "/funcionario/detalhe?id=" + id;
    }

    function montaTabela( )
    {
        let tabela = new Tabela( "tabela_funcionarios", acoes );

        tabela.addCampo( "Nome", "nome" );
        tabela.addCampo( "Email", "email", "" );
        tabela.addCampo( "Telefone", "telefone", "", (telefone) => { return mascaraTextoTelefone( telefone ) } );
        tabela.setURL( BASE_URL + "/api/funcionario" );
        tabela.setPaginado( true );
        tabela.setFiltro( "filtro_funcionarios", formatacaoData );

        tabela.render( );
    }
    
</script>