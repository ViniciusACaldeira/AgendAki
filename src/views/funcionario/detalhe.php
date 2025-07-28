<?php
    use Vennizlab\Agendaki\models\FuncionarioModel;
    use Vennizlab\Agendaki\models\ServicoModel;
    use Vennizlab\Agendaki\helpers\Flash;
    Flash::print( );
     
    $id = $_GET['id'];
    
    $funcionarioModel = new FuncionarioModel();
    $servicoModel = new ServicoModel( );

    $funcionario = $funcionarioModel->getById( $id );

    $servicos = $servicoModel->getAll();
    $servicosFuncionario = $servicoModel->getByFuncionario( $id );
    $idsFuncionario = array_column($servicosFuncionario, 'id');

    foreach( $servicos as $index => $servico )
        if( in_array($servico['id'], $idsFuncionario) )
            $servicos[$index]['checked'] = true;
?>

<a href="/funcionario">Voltar</a>

<h1>Detalhe <?= $funcionario['nome'] ?></h1>
<p>Email: <?= $funcionario['email'] ?></p>
<p>Telefone: <?= $funcionario['telefone'] ?></p>

<h2>Serviços</h2>

<form action="atualizarServico" method="POST">
    <input type="hidden" name="funcionario_id" id="funcionario_id" value="<?= $id ?>">

    <?php foreach ($servicos as $servico): ?>
        <?php 
            $nome = htmlspecialchars($servico['nome']);
            $checked = !empty($servico['checked']) ? 'checked' : '';
        ?>
        <div>
            <input type="checkbox" name="servicos[]" id="<?= $nome ?>" value="<?= $servico['id'] ?>" <?= $checked ?>>
            <label for="<?= $nome ?>"><?= $nome ?></label>
        </div>
    <?php endforeach; ?>

    <button type="submit">Salvar</button>
</form>


