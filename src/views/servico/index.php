<?php
    use Vennizlab\Agendaki\helpers\Flash;
use Vennizlab\Agendaki\models\ServicoModel;

    Flash::print( ); 
?>

<a href="/dashboard">Voltar</a>

<h1>Serviço</h1>
<a href="/servico/cadastro">Cadastrar serviço</a>

<br>
<?php

$model = new ServicoModel( );
$servicos = $model->getAll();

if( is_array($servicos))
{
    foreach( $servicos as $servico )
        echo $servico['nome'].' - '.$servico['descricao'].'</br>';
}
?>