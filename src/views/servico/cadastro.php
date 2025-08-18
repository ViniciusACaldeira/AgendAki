<?php
    use Vennizlab\Agendaki\helpers\Flash;
    Flash::print( ); 
?>

<a href="/servico">Voltar</a>

<form action="/servico/cadastrar" method="POST">
    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome" require >
    <br>
    <label for="descricao]">Descrição:</label>
    <textarea name="descricao" id="descricao"></textarea>
    <br>
    <label for="preco">Preço</label>
    <input type="number" id="preco" name="preco" min="0.00" step="0.01">

    <button type="submit">Cadastrar</button>
</form>

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
            console.log( data );
            console.log( data['data'] );
        })
        .catch( error => console.log( error ) );
    });

</script>