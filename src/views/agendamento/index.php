<a href="dashboard">Voltar</a>

<h1>Agendamento</h1>
<a href="agendamento/cadastro">Cadastrar agendamento</a>

<section id="filtro">
    <label for="data">Data</label>
    <input type="date" name="data" id="data"/>

    <label for="funcionarios">Funcionários:</label>
    <select name="funcionarios" id="funcionarios" multiple>
        <option value="">Nenhum</option>
        <?php foreach($data["funcionarios"] as $funcionario): ?>
            <option value="<?= $funcionario['id']?>"><?= $funcionario['nome'] ?></option>
        <?php endforeach;?>
    </select>

    <label for="servicos">Serviços:</label>
    <select name="servicos" id="servicos" multiple>
        <option value="">Nenhum</option>
        <?php foreach($data["servicos"] as $servico): ?>
            <option value="<?= $servico['id']?>"><?= $servico['nome'] ?></option>
        <?php endforeach;?>
    </select>

    <label for="clientes">Clientes:</label>
    <select name="clientes" id="clientes" multiple>
        <option value="">Nenhum</option>
        <?php foreach($data["clientes"] as $cliente): ?>
            <option value="<?= $cliente['id']?>"><?= $cliente['nome'] ?></option>
        <?php endforeach;?>
    </select>

    <button type="button" onclick="listar()">Buscar</button>
</section>

<table>
    <thead>
        <th>Data</th>
        <th>Horário</th>
        <th>Serviço</th>
        <th>Funcionário</th>
        <th>Usuário</th>
    </thead>

    <tbody id="agendamentos">

    </tbody>
</table>

<script>
    function listar( )
    {
        const data = document.getElementById( "data" ).value;
        const servicos = getOpcoesSelecionadas( document.getElementById( "servicos" ) );
        const funcionarios = getOpcoesSelecionadas( document.getElementById( "funcionarios") );
        const clientes = getOpcoesSelecionadas( document.getElementById( "clientes" )) ;

        const formData = new FormData( );
        formData.append("data", data);
        formData.append("funcionarios_id", funcionarios );
        formData.append("servicos_id", servicos);
        formData.append("usuarios_id", clientes);

        fetch(`http://localhost:8000/api/agendamento`, {
            method: "POST",
            body: formData
        })
        .then( response => response.json() )
        .then( data => {
            montaLista( data['data'] );
        })
        .catch( error => {
            console.error( "Erro na requisição: ", error );
        });
    }

    function montaLista( agendas )
    {
        const tabela = document.getElementById("agendamentos");
        tabela.innerText = "";

        agendas.forEach( (data, index) => {
            const tr = document.createElement("tr");
            tr.appendChild( criaLinha(data['Data']));
            tr.appendChild( criaLinha( `${data['Inicio_Agendamento']} - ${data['Fim_Agendamento']}`));
            tr.appendChild( criaLinha(data['Nome_Servico']));
            tr.appendChild( criaLinha(data['Nome_Funcionario']));
            tr.appendChild( criaLinha(`${data['Nome']} - ${data['Telefone']}`));
            
            tabela.appendChild( tr );
        });
    }

    function criaLinha( valor )
    {
        const td = document.createElement("td");
        td.innerText = valor;

        return td;
    }

    function getOpcoesSelecionadas( select )
    {
        const options = select.options;
        let selecionados = [];
        
        for( let i = 0; i < options.length; i++ )
            if( options[i].selected )
                selecionados.push( options[i].value );

        return selecionados
    }
</script>