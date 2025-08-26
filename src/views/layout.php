<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgendAki<?= $titulo ?? "" ?></title>
    <link rel="stylesheet" href="/assets/styles/layout.css">
    <link rel="stylesheet" href="/assets/styles/toast.css">
</head>
<body>
    <script src="/assets/script/validador.js"></script>
    <script src="/assets/script/mascara.js"></script>
    <script src="/assets/script/toast.js"></script>
    <script src="/assets/script/util.js"></script>
    <script src="/assets/script/agendaki.js.php"></script>

    <div id="toast-container"></div>
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