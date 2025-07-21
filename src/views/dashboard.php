<?php
    use Vennizlab\Agendaki\helpers\Flash;
    Flash::print( ); 
?>

<h1>dashboard</h1>

<nav>
    <ul>
        <li>
            <a href="/funcionario">Funcionário</a>
            <ul>
                <li><a href="/funcionario/cadastro">Cadastrar Funcionário</a></li>
            </ul>
        </li>
    </ul>
</nav>

<a href="auth/logout">Logout</a>