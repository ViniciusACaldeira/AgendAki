<link rel="stylesheet" href="/assets/styles/cadastro.css">

<a href="/servico">Voltar</a>

<section>
    <h1>Cadastro</h1>

    <form id="form_cadastro_servico" method="POST">
        <div class="field">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" require >
        </div>
        
        <div class="field">
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao"></textarea>
        </div>
        
        <div class="field">
            <label for="preco">Preço</label>
            <input type="number" id="preco" name="preco" min="0.00" step="0.01">
        </div>
        
        <div class="field">
            <label for="duracao">Duração</label>
            <input type="text" data-type="time" name="duracao" id="duracao">
        </div>
        <button type="submit">Cadastrar</button>
    </form>
</section>

<script src="/assets/script/modal.js"></script>
<script>
    document.querySelector('form').addEventListener( 'submit', async (event) =>{
        event.preventDefault( );

        const formData = new FormData( event.target );

        const response = await fetch('/api/servico/cadastrar',{
            method: 'POST',
            body: formData
        })
        .then( response => response.json() )
        .then( data => {
            const status = data['status'];
            const erros = data['data']['erros'];
            
            if( erros != undefined )
                erros.forEach( (e) => { mostrarToast( e, TOAST_ERRO ) } );
            else
                mostrarModal( );
        })
        .catch( error => console.log( error ) );
    });

    function limpaForm( )
    {
        document.querySelector( "#nome" ).value = "";
        document.querySelector( "#descricao" ).value = "";
        document.querySelector( "#preco" ).value = "";
        document.querySelector( "#duracao" ).value = "";
    }

    function mostrarModal( )
    {
        modal_abrir( { titulo: "Cadastrar outro?", botoes: [ {texto: "Voltar a tela de início", acao: (modal) => { redireciona( "/servico" ) } }, {texto: "Cadastrar outro", acao: (modal) => { limpaForm(); modal_fechar( ) }}]} )
    }
</script>