<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgendAki<?= $titulo ?? "" ?></title>
    <link rel="stylesheet" href="/assets/styles/layout.css">
</head>
<body>
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
            <li class="logout">
                <a href="auth/logout" class="logout-link">Logout</a>
            </li>
        </ul>
    </nav>

    <main>
        <?= $conteudo ?>
    </main>
</body>
</html>