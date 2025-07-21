<?php
    use Vennizlab\Agendaki\helpers\Flash;
    Flash::print( ); 
?>

<a href="/funcionario">Voltar</a>

<section>
    <h1>Cadastro</h1>
    <form action="/funcionario/cadastrar" method="post">
        <label for="nome">Nome</label>
        <input type="text" name="nome" id="nome">

        <label for="telefone">Telefone</label>
        <input type="text" name="telefone" id="telefone">

        <label for="email">Email</label>
        <input type="email" name="email" id="email">

        <label for="senha">Senha</label>
        <input type="password" name="senha" id="senha">

        <label for="senha_confirmar">Senha confirmar</label>
        <input type="password" name="senha_confirmar" id="senha_confirmar">

        <button type="submit">Cadastrar</button>
    </form>
</section>