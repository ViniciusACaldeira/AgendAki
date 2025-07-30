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
    $mapaServicosFuncionario = [];
    foreach ($servicosFuncionario as $sf)
        $mapaServicosFuncionario[$sf['id']] = $sf;

    // Marca os serviços existentes e adiciona duração
    foreach ($servicos as $index => $servico) {
        $idServico = $servico['id'];
        if (isset($mapaServicosFuncionario[$idServico])) {
            $servicos[$index]['checked'] = true;
            $servicos[$index]['duracao'] = $mapaServicosFuncionario[$idServico]['duracao'];
        }
    }
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
            <input type="checkbox" name="servicos[]" id="<?= $servico['id'] ?>" value="<?= $servico['id'] ?>" <?= $checked ?>>
            <label for="<?= $servico['id'] ?>"><?= $nome ?></label>
            
            <label for="servicos_duracao_<?= $servico['id'] ?>">Duração</label>
            <input type="time" name="servicos_duracao[]" id="servicos_duracao_<?= $servico['id'] ?>" value="<?= $servico['duracao'] ?>">
        </div>
    <?php endforeach; ?>

    <button type="submit">Salvar</button>
</form>


