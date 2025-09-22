
<link rel="stylesheet" href="/assets/styles/servico/index.css">
<a href="/dashboard" class="btn-voltar">Voltar</a>

<h1>Serviço</h1>
<a href="/servico/cadastro">Cadastrar serviço</a>

<br>
<link rel="stylesheet" href="/assets/styles/tabela.css">

<form id="filtro_servicos" method="GET">
    <div class="field-inline">
        <input type="checkbox" name="inativo" id="inativo" checked>
        <label for="inativo">Considerar Inativo</label>
    </div>
  
    <button type="submit">Consultar</button>
</form>
<table id="tabela_servicos">

</table>

<script src="/assets/script/tabela.js"></script>
<script>
    window.addEventListener('DOMContentLoaded', () => { listar( ) });
    
    const acoes = [
        (item) => {
            const btnDetalhar = document.createElement( "button" );
            btnDetalhar.textContent = "Detalhar";

            btnDetalhar.onclick = () => detalhar( item.id );

            return btnDetalhar;
        },
        (item) => {
            const btnInativar = document.createElement("button");
            if( item.ativo != "1" )
                btnInativar.hidden = true;

            btnInativar.textContent = "Inativar";
            btnInativar.style.marginLeft = "5px";

            btnInativar.addEventListener("click", () => {
                inativar( item.id );
            });

            return btnInativar;
        }
    ];

    function inativar( id )
    {
        formData = new FormData( );
        formData.append( "id", id );

        fetch( `/api/servico/inativar`,{
            method: 'post',
            body: formData
        } )
        .then( response => response.json( ))
        .then( response => {
            const status = response['status'];
            const data = response['data'];
            const mensagem = data['mensagem'];

            mostrarToast( mensagem, status == 200 ? TOAST_SUCESSO : TOAST_ERRO );
        })
        .catch( (error) => { console.log( "Falha ao inativar o serviço.", error )});
    }

    function listar( )
    {
        let tabela = new Tabela( "tabela_servicos", acoes );

        tabela.addCampo( "Nome", "nome" );
        tabela.addCampo( "Descrição", "descricao", "" );
        tabela.addCampo( "Preço", "preco", "", (texto) => { return ajustaPreco(texto)} );
        tabela.setURL( BASE_URL + "/api/servico" );
        tabela.setPaginado( true );
        tabela.setFiltro( "filtro_servicos" );

        tabela.render( );
    }

    function ajustaPreco( texto )
    {
        return new Intl.NumberFormat( "pt-BR", { style: "currency", currency: "BRL" } ).format( texto ) ; 
    }

    function montaAcao( table )
    {
        console.log( "teste" );
        console.log( table );
    }

    function detalhar( id )
    {
        window.location = BASE_URL + "/servico/detalhe?id=" + id;
    }
</script>