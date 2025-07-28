<a href="/dashboard">voltar</a>

<h1>Funcionário</h1>
<a href="/funcionario/cadastro">Cadastrar Funcionário</a>
<?php

use Vennizlab\Agendaki\models\FuncionarioModel;
use Vennizlab\Agendaki\helpers\Flash;
Flash::print( );

$funcionario = new FuncionarioModel( );

$funcionarios = $funcionario->getAll( );

?>

<table>
    <thead>
        <th>Nome</th>
        <th>Email</th>
        <th>Telefone</th>
        <th>Ação</th>
    </thead>
    <tbody>
        <?php foreach ($funcionarios as $funcionario): ?>
            <?php 
                $nome = htmlspecialchars($funcionario['nome']);
            ?>
            <tr>
                <td><?= $nome ?></td>
                <td><?= $funcionario['email']?></td>
                <td><?= $funcionario['telefone']?></td>
                <td><a href="funcionario/detalhe?id=<?= $funcionario['id'] ?>">detalhe</a></td>
            </tr>
        <?php endforeach; ?>

    </tbody>
</table>