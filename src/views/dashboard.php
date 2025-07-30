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
        <li>
            <a href="/servico">Serviço</a>
            <ul>
                <li><a href="/servico/cadastro">Cadastrar Serviço</a></li>
            </ul>
        </li>
        <li>
            <a href="/agenda">Agenda</a>
            <ul>
                <li><a href="/agenda/cadastro">Cadastrar Agenda</a></li>
            </ul>
        </li>
        <li>
            <a href="/agendamento">Agendamento</a>
            <ul>
                <li><a href="/agendamento/cadastro">Cadastrar Agendamento</a></li>
            </ul>
        </li>
    </ul>
</nav>

<a href="auth/logout">Logout</a>