<style>
    input[readonly], textarea[readonly] 
    {
        border: none;
        background: transparent;
        color: #333;
        font-size: 1rem;
        padding: 4px 0;
        pointer-events: none;
    }

    input:not([readonly]), textarea:not([readonly]) 
    {
        border: 1px solid #ccc;
        background: #fff;
        padding: 6px;
        pointer-events: auto;
    }

    .field 
    {
        margin-bottom: 10px;
    }

    label 
    {
        display: block;
        font-weight: bold;
        margin-bottom: 4px;
    }
</style>


<a href="/servico" class="btn-voltar">Voltar</a>

<h1 id="nome_servico">Serviço</h1>

<button id="editar">Editar</button>
<button id="cancelar" hidden>Cancelar</button>

<form id="form">
    <input type="number" id="id" name="id" hidden>

    <div class="field">
        <label>Nome:</label>
        <span id="nome" data-field="nome" data-type="text"></span>
    </div>

    <div class="field">
        <label>Descrição:</label>
        <span id="descricao" data-field="descricao" data-type="textarea"></span>
    </div>

    <div class="field">
        <label>Preço:</label>
        <span id="preco" data-editable="false" data-field="preco" data-type="number" data-step="0.01" data-min="0"></span>
    </div>

    <button id="form_salvar" type="submit" hidden>Salvar</button>
</form>

<script>
    const form = document.getElementById( "form" );

    document.addEventListener("DOMContentLoaded", function( ) 
    {
        document.getElementById( "editar" ).addEventListener( "click", () => ajustaBotoes( true ) );
        document.getElementById( "cancelar" ).addEventListener( "click", () => ajustaBotoes( false ) );
        document.querySelector( "form" ).addEventListener( "submit", async (event) =>{ editar( event ); });

        getProduto( );
    });

    async function editar( event )
    {
        event.preventDefault( );

        const formData = new FormData( event.target );

        const response = await fetch('/api/servico/editar',{
            method: 'POST',
            body: formData
        })
        .then( response => response.json() )
        .then( data => {
            console.log( data );
            mostrarToast( data['data']['mensagem'], TOAST_SUCESSO );
            converteData( data['data']['data'][0] );
            ajustaBotoes( false );
        })
        .catch( error => console.log( error ) );
    }

    function converteData( data )
    {
        data_old.id = data.id;
        data_old.nome = data.nome;
        data_old.descricao = data.descricao;
        data_old.ativo = data.ativo;
        data_old.preco = data.preco;
    }
    let data_old = [];

    function getProduto( )
    {
        const parametros = getParametros( );
        const id = parametros.id;

        document.getElementById( "id" ).value = id;

        fetch( BASE_URL + `/api/servico?id=${id}&inativo=on`, {
            method: 'get'
        })
        .then( response => response.json( ) )
        .then( data => {
            data_old = data['data'][0];
            montaTela( data['data'][0] );
        })
        .catch( (error) => console.error( "Falha ao coletar o produto.", error ) );

        getPreco( );
    }

    function getPreco( )
    {
        const parametros = getParametros( );
        const id = parametros.id;

        fetch( BASE_URL + `/api/servico/preco?servico_id=${id}`,{
            method: 'get'
        })
        .then( response => response.json( ) )
        .then( data => {
            console.log(data['data'])
        })
        .catch( (error) => console.error( "Falha ao coletar o histórico de Preço.", error ) );
    }

    function montaTela( data )
    {
        const nome_servico = document.getElementById( "nome_servico" );
        nome_servico.textContent = data.nome;

        const nome = document.getElementById( "nome" );
        nome.textContent = data.nome;

        const descricao = document.getElementById( "descricao" );
        descricao.textContent = data.descricao;

        const preco = document.getElementById( "preco" );
        preco.textContent = data.preco;
    }

    function ajustaBotoes( editar )
    {
        const btnEditar = document.getElementById( "editar" );
        const salvar = document.getElementById( "form_salvar" );
        const cancelar = document.getElementById( "cancelar" );

        setVisibilidade( btnEditar, editar );
        setVisibilidade( salvar, !editar );
        setVisibilidade( cancelar, !editar );

        if( editar )
            transformarParaInputs( );
        else
        {
            transformarParaSpans( );
            montaTela( data_old );
        }
    }

    function setVisibilidade( elemento, esconde )
    {
        elemento.hidden = esconde;
    }

</script>