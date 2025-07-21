<?php
    use Vennizlab\Agendaki\helpers\Flash;
    Flash::print( ); 
?>

<a href="/servico">Voltar</a>

<form action="/servico/cadastrar" method="POST">
    <label for="nome_servico">Nome:</label>
    <input type="text" id="nome_servico" name="nome_servico">
    <br>
    <label for="descricao_servico">Descrição:</label>
    <textarea name="descricao_servico" id="descricao_servico"></textarea>
    
    <button type="submit">Cadastrar</button>
</form>