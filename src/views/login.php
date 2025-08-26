<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgendAki - Login</title>
</head>
<body>

<link rel="stylesheet" href="/assets/styles/login.css">
<link rel="stylesheet" href="/assets/styles/toast.css">
<div id="toast-container"></div>

<section id="sessao_login">
    <form id="form_login">
        <div>
            <label for="login">Login</label>
            <input type="text" id="login" name="login">
        </div>

        <div>
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha">
        </div>

        <button type="submit">Login</button>

        <p style="margin-top:10px; text-align:center;">
            Não tem conta? 
            <button type="button" class="link-button" onclick="ativa('cadastro')">Cadastrar-se</button>
        </p>
    </form>
</section>

<section id="sessao_cadastro" hidden>
    <form id="form_cadastro">
        <div>
            <label for="cadastro_nome">Nome</label>
            <input type="text" id="cadastro_nome" name="nome">
        </div>

        <div>
            <label for="cadastro_telefone">Telefone</label>
            <input type="text" id="cadastro_telefone" name="telefone">
        </div>

        <div>
            <label for="cadastro_email">Email</label>
            <input type="email" id="cadastro_email" name="email">
        </div>

        <div>
            <label for="cadastro_senha">Senha</label>
            <input type="password" id="cadastro_senha" name="senha">
        </div>

        <div>
            <label for="cadastro_senha_confirmar">Confirmar senha</label>
            <input type="password" id="cadastro_senha_confirmar" name="senha_confirmar">
        </div>

        <button type="submit">Cadastrar</button>

        <p style="margin-top:10px; text-align:center;">
            Já tem conta? 
            <button type="button" class="link-button" onclick="ativa('login')">Login</button>
        </p>
    </form>
</section>

<script src="/assets/script/agendaki.js.php"></script>
<script src="/assets/script/toast.js"></script>
<script>
    const sessoes = ['login', 'cadastro'];

    document.getElementById( "form_cadastro" ).addEventListener( "submit", cadastrar );
    document.getElementById( "form_login" ).addEventListener( "submit", login );
    document.getElementById( "cadastro_telefone" ).addEventListener( "input", (e) => mascaraTelefone(e) );
    document.addEventListener( "DOMContentLoaded", ( ) => { ativa("login"); } );
    
    function ativa( tipo )
    {
        for( let i = 0; i < sessoes.length; i++ )
        {
            let sessao = document.getElementById( `sessao_${sessoes[i]}` );
            const outra = sessoes[i] != tipo;
            sessao.hidden = outra;

            if( outra )
                sessao.classList.remove('active');
            else
                sessao.classList.add('active');
        }
    }

    function login( event )
    {
        event.preventDefault( );

        const formData = new FormData( event.target );

        fetch( BASE_URL + "/api/auth/login", {
            method: "POST",
            body: formData
        })
        .then( response => response.json() )
        .then( data => {
            const retorno = data['data'];
            if( data['status'] != 200 )
                mostrarToast( retorno['mensagem'], TOAST_ERRO );
            else
                window.location = '/dashboard';
        })
        .finally( )
        .catch( error => {
            console.error( "Erro na requisição: ", error );
        });
    }

    function cadastrar( event )
    {
        event.preventDefault( );
        const telefone = event.target.querySelector("#cadastro_telefone").value;

        const formData = new FormData( event.target );
        formData.set( "telefone", desmascararTelefone( telefone ) );

        fetch( BASE_URL + "/api/auth/cadastrar", {
            method: 'post',
            body: formData,
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
            {
                mostrarToast( mensagem, status == 200 ? TOAST_SUCESSO : TOAST_ERRO );
                ativa('login');
            }
        })
        .catch( (error) => {
            console.log( error );
            mostrarToast( "Falha ao cadastrar o usuário.", TOAST_ERRO);
        });
    }
</script>
    
</body>
</html>
