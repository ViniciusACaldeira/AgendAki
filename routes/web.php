<?php

use Vennizlab\Agendaki\controllers\FuncionarioController;
use Vennizlab\Agendaki\controllers\HomeController;
use Vennizlab\Agendaki\controllers\UsuarioController;

// Lista de rotas: 'url' => [Classe, 'método']
return [
    '/usuario/index' => [UsuarioController::class, 'index'],
    '/auth/login' => [UsuarioController::class, 'login'],
    '/auth/logout' => [UsuarioController::class, 'logout'],
    '/auth/cadastrar' => [UsuarioController::class, 'cadastrar'],
    '/dashboard' => [HomeController::class, 'dashboard'],
    '/funcionario' => [FuncionarioController::class, 'index'],
    '/funcionario/cadastro' => [FuncionarioController::class, 'cadastro'],
    '/funcionario/cadastrar' => [FuncionarioController::class, 'cadastrar'],
    // Adicione outras rotas aqui
];
