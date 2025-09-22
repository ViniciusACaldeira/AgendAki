<link rel="stylesheet" href="/assets/styles/cadastro.css">

<a class="btn-voltar" href="/perfil">Voltar</a>

<h1>Alterar Senha</h1>

<form id="form_cadastro_senha" method="POST">
    <div class="field">
        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha">
    </div>

    <p id="mensagem" style="color:red;"></p>

    <div class="field">
        <label for="senha_confirmar">Senha Confirmar:</label>
        <input type="password" name="senha_confirmar" id="senha_confirmar">
    </div>

    <button type="submit">Alterar</button>
</form>

<script>
    document.querySelector( "form" ).addEventListener( "submit", salvar );
    const mensagem = document.getElementById('mensagem');

    const senha = document.getElementById( "senha" );
    senha.addEventListener( "input", validaSenha );

    const senha_confirmar = document.getElementById( "senha_confirmar" );
    senha_confirmar.addEventListener( "input", validaSenha );
    
    function validaSenha( e )
    {
        if( senha_confirmar.value === "" )
        {
            mensagem.textContent = "";
            senha_confirmar.style.borderColor = "";
        }
        else
        {
            if( senha.value == senha_confirmar.value )
            {
                mensagem.textContent = "Senhas compatíveis!";
                mensagem.style.color = 'green'
                senha_confirmar.style.borderColor = "green";
            }
            else
            {
                mensagem.textContent = "Senhas não coincidem!";
                mensagem.style.color = 'red'
                senha_confirmar.style.borderColor = "red";
            }
        }
    }

    function salvar( e )
    {
        e.preventDefault( );

        if( senha.value !== senha_confirmar.value )
            return;

        const formData = new FormData( e.target );
        postAPI( "/api/auth/senha", formData, (data) => { mostrarToast( data, TOAST_SUCESSO ); e.target.reset( ); validaSenha( ) }, "Falha ao alterar a senha." );
    }

</script>