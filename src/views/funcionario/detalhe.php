<link rel="stylesheet" href="/assets/styles/funcionario/detalhe.css">
<a href="/funcionario" class="btn-voltar">Voltar</a>

<h1 id="nome">Detalhe </h1>
<p id="email">Email: </p>
<p id="telefone">Telefone: </p>

<h2>Serviços</h2>

<section id="servicos">
    <form id="form_cadastro_servicos" method="POST">
        <input type="hidden" name="funcionario_id" id="funcionario_id" value="">

        <section id="form_servicos">
            <input type="search" id="buscar_servico" placeholder="Buscar serviço..." />
        </section>

        <button type="submit">Salvar</button>
    </form>
</section>

<script src="/assets/script/util.js"></script>
<script>
    const parametros = getParametros( );
    document.getElementById( "form_cadastro_servicos" ).addEventListener( "submit", editar );

    window.addEventListener( "DOMContentLoaded", () =>{
        document.getElementById( "funcionario_id" ).value = parametros.id;
        coletarFuncionario( );
        coletarServicos( );
    });

    function editar( e )
    {
        e.preventDefault( );

        const formData = new FormData( e.target );

        fetch( BASE_URL + "/api/servico/funcionario/cadastrar", {
                method: "post",
                body: formData
            }
        )
        .then( response => response.json( ) )
        .then( response => {
            const data = response['data'];
            const status = response['status'];
            const erros = response['erros'];

            if( erros !== undefined )
                erros.forEach( e => { mostrarToast( e, TOAST_ERRO ); } );
            else
                mostrarToast( data, status == 200 ? TOAST_SUCESSO : TOAST_ERRO );
        })
        .catch( (error) => { console.error(error) } );
    }

    function coletarFuncionario( )
    {
        fetch( BASE_URL + "/api/funcionario?id=" + parametros.id )
        .then( response => response.json( ) )
        .then( response => {
            const data = response['data'][0];
            const status = response['status'];

            if( status == 200 )
            {
                const nome = document.getElementById( "nome" );
                nome.textContent = nome.textContent + data['nome'];

                const email = document.getElementById( "email" );
                email.textContent = email.textContent + data['email'];

                const telefone = document.getElementById( "telefone" );
                telefone.textContent = telefone.textContent + formatarTelefone( data['telefone'] );
            }
        })
        .catch( (error) => console.error( error ));
    }

    async function coletarServicos( )
    {
        fetch( BASE_URL + "/api/servico" )
        .then( response => response.json( ) )
        .then( response => {
            const status = response['status'];
            const data = response['data'];

            if( status == 200 )
                coletarServicosFuncionario( data );
            else
                mostrarToast( data['mensagem'], TOAST_ERRO );
        })
        .catch( (error) => console.error( error ) );
    }

    async function coletarServicosFuncionario( servicos )
    {
        let servicosFuncionario = [];

        await fetch( BASE_URL + "/api/servico/funcionario?id=" + parametros.id )
        .then( response => response.json( ) )
        .then( response => {
            const data = response['data'];
            const status = response['status'];

            if( status == 200 )
                servicosFuncionario = data;
        })
        .catch( (error) => console.error( error) );

        montaServicos( servicos, servicosFuncionario );
    }

    function montaServicos( servico, servicosFuncionario )
    {
        const form = document.getElementById( "form_servicos" );
        servico.forEach( s => {
            const sfuncionario = servicosFuncionario.find( sf => sf.id == s.id );

            const div = document.createElement( "div" );
            div.className = "field";

            const checkbox = document.createElement( "input" );
            checkbox.type = "checkbox";
            checkbox.name = "servico[]";
            checkbox.id = `servico_${s.id}`;
            checkbox.value = s.id;
            checkbox.checked = sfuncionario;
            checkbox.onchange = (e) => { ajustaDuracao(e.target) };

            const label = document.createElement( "label" );
            label.htmlFor = `servico_${s.id}`;
            label.textContent = s.nome;

            div.appendChild( checkbox );
            div.appendChild( label );

            const labelDuracao = document.createElement( "label" );
            labelDuracao.htmlFor = `duracao_${s.id}`;
            labelDuracao.textContent = "Duração";

            const duracao = document.createElement( "input" );
            duracao.type = "text";
            duracao.dataset.type = "time";
            duracao.id = `duracao_${s.id}`;
            duracao.name = "duracao[]";
            const tempo = sfuncionario ? sfuncionario.duracao : s.duracao;
            duracao.value = formatarTempo( tempo );
            duracao.disabled = !sfuncionario;

            div.appendChild( labelDuracao );
            div.appendChild( duracao );

            form.appendChild( div );
        });
    }

    function ajustaDuracao( event )
    {
        const id = event.value;
        const duracao = document.getElementById( "duracao_" + id );
        duracao.disabled = !event.checked;
    }

    document.getElementById("buscar_servico").addEventListener("input", function( ) {
        const termo = this.value.toLowerCase();
        const campos = document.querySelectorAll( "#form_servicos .field" );

        campos.forEach(campo => {
            const labelServico = campo.querySelector('label[for^="servico_"]');
            const nome = labelServico ? labelServico.textContent.toLowerCase() : "";

            if( nome.includes( termo ) ) 
                campo.style.display = "flex";
            else
                campo.style.display = "none";
        });
    });
</script>
