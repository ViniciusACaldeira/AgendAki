<style>
    form.editavel {
        pointer-events: none;
        max-width: 400px;
        margin-top: 20px;
    }

    form.editavel input {
        border: none;
        background: transparent;
        color: #333;
        font-size: 1rem;
        padding: 6px;
        pointer-events: none;
        width: 100%;
        box-sizing: border-box;
    }

    form.editavel input:disabled,
    form.editavel input {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    form.editavel button[type="submit"],
    form.editavel button[id="cancelar"] {
        display: none;
    }

    form.editavel.ativa {
        pointer-events: auto;
    }

    form.editavel.ativa input {
        border: 1px solid #ccc;
        background: #fff;
        pointer-events: auto;
        border-radius: 4px;
    }

    form.editavel.ativa button[type="submit"],
    form.editavel.ativa button[id="cancelar"] {
        display: inline-block;
        margin-top: 10px;
        padding: 8px 16px;
        font-size: 0.9rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }

    form.editavel.ativa button[type="submit"] {
        background-color: #4CAF50;
        color: #fff;
        margin-right: 10px;
    }

    form.editavel.ativa button[type="submit"]:hover {
        background-color: #45a049;
    }

    form.editavel.ativa button#cancelar {
        background-color: #f44336;
        color: #fff;
    }

    form.editavel.ativa button#cancelar:hover {
        background-color: #da190b;
    }

    #editar {
        padding: 8px 16px;
        font-size: 0.9rem;
        border: none;
        border-radius: 4px;
        background-color: #2196F3;
        color: #fff;
        cursor: pointer;
        transition: background 0.2s;
        margin-bottom: 10px;
    }

    #editar:hover {
        background-color: #0b7dda;
    }

    a[href="/alterarSenha"] {
        padding: 8px 16px;
        font-size: 0.9rem;
        border: none;
        border-radius: 4px;
        background-color: #FF9800;
        color: #fff;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 10px;
        margin-bottom: 10px;
        text-decoration: none;
    }

    a[href="/alterarSenha"]:hover {
        background-color: #e68900;
    }

    .escondido {
        display: none;
    }

    form.editavel div {
        margin-bottom: 10px;
    }

    form.editavel label {
        display: block;
        margin-bottom: 4px;
        font-weight: 500;
    }

    .acoes{
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
</style>

<h1>Meu Perfil</h1>

<div class="acoes">
    <a href="/alterarSenha">Alterar Senha</a>
    <button type="button" id="editar">Editar</button>
</div>


<form class="editavel" id="form_cadastro_usuario">
    <div class="field">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome">
    </div>

    <div class="field">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email">
    </div>

    <div class="field">
        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" name="telefone">
    </div>

    <button type="submit">Alterar</button>
    <button type="button" id="cancelar">Cancelar</button>

</form>

<script>
    window.addEventListener( "DOMContentLoaded", async ( ) => {
        document.getElementById( "telefone" ).addEventListener( "input", mascaraTelefone );
        document.querySelector( "form" ).addEventListener( "submit", alterar );
        document.getElementById( "cancelar").addEventListener( "click", () => ativaEdicao( false ) );
        document.getElementById( "editar").addEventListener( "click", () => ativaEdicao( true ) );

        await coletaUsuario( );
    });

    const dados = [
        { 
            "elemento": document.getElementById( "nome" ),
            "antigo": "" 
        },
        {
            "elemento": document.getElementById( "telefone" ),
            "antigo": ""
        },
        {
            "elemento": document.getElementById( "email" ),
            "antigo": ""
        }
    ]

    function alterar( e )
    {
        e.preventDefault( );

        const formData = new FormData( e.target );
        formData.set( "telefone", desmascararTelefone( formData.get( "telefone" ) ) );

        postAPI( "/api/usuario/alterar", formData, (data) => { salvaDados( ); ativaEdicao(false); mostrarToast( data, TOAST_SUCESSO ); }, "Alterado com sucesso." );
    }

    async function coletaUsuario( )
    {
        getAPI( "/api/usuario", montaUsuario, "Falha ao coletar o usuário." );
    }

    function montaUsuario( data )
    {
        const nome = data['nome'];
        const email = data['email'];
        const telefone = data['telefone'];

        document.getElementById( "nome" ).value = nome;
        document.getElementById( "email" ).value = email;
        document.getElementById( "telefone" ).value = mascaraTextoTelefone( telefone );

        salvaDados( );
    }

    function salvaDados( )
    {
        dados.forEach( d => d.antigo = d.elemento.value );
    }

    function recuperaDados( )
    {
        dados.forEach( d => d.elemento.value = d.antigo );
    }

    function ativaEdicao( ativa )
    {
        if( ativa )
        {
            document.querySelector( "form" ).classList.add( "ativa" );
            document.getElementById( "editar" ).classList.add( "escondido" );
        }
        else
        {
            recuperaDados( );
            document.querySelector( "form" ).classList.remove( "ativa" );
            document.getElementById( "editar" ).classList.remove( "escondido" );
        }   
    }

</script>