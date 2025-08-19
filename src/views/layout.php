<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgendAki<?= $titulo ?? "" ?></title>
    <link rel="stylesheet" href="/assets/styles/layout.css">
</head>
<body>
    <?php
        use Vennizlab\Agendaki\helpers\Menu;

        $menu = new Menu( );

        echo $menu->toHTML( );
    ?>
    

    <main>
        <?= $conteudo ?>
    </main>
</body>
</html>