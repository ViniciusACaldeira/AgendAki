<link rel="stylesheet" href="/assets/styles/cadastro.css">

<a href="/funcionario">Voltar</a>

<section>
    <h1>Cadastro</h1>

    <form method="POST" id="form_cadastro_funcionario">
        <div class="field">
            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome">
        </div>

        <div class="field-group">
            <div class="field">
                <label for="telefone">Telefone</label>
                <input type="text" name="telefone" id="telefone">
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" name="email" id="email">
            </div>
        </div>

        <div class="field-group">
            <div class="field">
                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha">
            </div>
            
            <div class="field">
                <label for="senha_confirmar">Confirmar Senha</label>
                <input type="password" name="senha_confirmar" id="senha_confirmar">
            </div>
        </div>
        
        <button type="submit">Cadastrar</button>
    </form>
</section>

<script src="/assets/script/util.js"></script>
<script src="/assets/script/modal.js"></script>
<script>
    document.getElementById( "form_cadastro_funcionario" ).addEventListener( "submit", cadastrar );
    document.getElementById( "telefone" ).addEventListener( "input", mascaraTelefone );

    function cadastrar( e )
    {
        e.preventDefault( );
        
        const formData = new FormData( e.target );
        formData.set( "telefone", desmascararTelefone( formData.get("telefone") ) );

        fetch( BASE_URL + "/api/funcionario/cadastrar",{
            method: "post",
            body: formData
        })
        .then( response => response.json( ) )
        .then( response => {
            const status = response['status'];
            const data = response['data'];
            const mensagem = data['mensagem'];

            if( data['erros'] !== undefined )
            {
                const erros = data['erros'];
                erros.forEach( e => mostrarToast( e, TOAST_ERRO ) );
            }
            else
                mostrarToast( mensagem, status == 200 ? TOAST_SUCESSO : TOAST_ERRO );

            if( status == 200 )
            {
                mostrarModal( );
            }
        })
        .catch( (error) => console.error( "Falha ao cadastrar usuário: ", error ) );
    }

    function limpaForm( )
    {
        document.querySelector( "#nome" ).value = "";
        document.querySelector( "#telefone" ).value = "";
        document.querySelector( "#email").value = "";
        document.querySelector( "#senha").value = "";
        document.querySelector( "#senha").value = "";
        document.querySelector( "#senha_confirmar").value = "";
    }

    function mostrarModal( )
    {
        modal_abrir( { titulo: "Cadastrar outro?", botoes: [ {texto: "Voltar a tela de início", acao: (modal) => { redireciona( "/funcionario" ) } }, {texto: "Cadastrar outro", acao: (modal) => { limpaForm(); modal_fechar( ) }}]} )
    }
</script>