
<?php

    use Vennizlab\Agendaki\helpers\Flash;

    if (Flash::has('erro'))
        echo '<p style="color:red;">' . Flash::get('erro') . '</p>';

    if (Flash::has('sucesso'))
        echo '<p style="color:green;">' . Flash::get('sucesso') . '</p>';
?>

<section>
    <h1>Login</h1>

    <form action="login" method="post">

        <label for="login">Telefone ou email</label>
        <input type="text" name="login" id="login">

        <label for="senha">Senha</label>
        <input type="password" name="senha" id="senha">

        <button type="submit">Entrar</button>
    </form>
</section>

<section>
    <h1>Cadastro</h1>

    <form action="cadastrar" method="post">
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
