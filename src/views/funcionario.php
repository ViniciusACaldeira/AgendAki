<a href="/dashboard">voltar</a>

<h1>Funcionário</h1>

<?php

use Vennizlab\Agendaki\models\FuncionarioModel;
use Vennizlab\Agendaki\helpers\Flash;
Flash::print( );

$funcionario = new FuncionarioModel( );

$funcionarios = $funcionario->getAll( );

if( is_array($funcionarios))
    foreach( $funcionarios as $func )
    {
        echo $func['nome'].$func["email"].$func["telefone"];
    }
?>

<br>

<a href="/funcionario/cadastro">Cadastrar Funcionário</a>