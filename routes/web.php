<?php

use Vennizlab\Agendaki\controllers\FuncionarioController;
use Vennizlab\Agendaki\controllers\HomeController;
use Vennizlab\Agendaki\controllers\UsuarioController;
use Vennizlab\Agendaki\controllers\ServicoController;

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
    '/servico' => [ServicoController::class, 'index'],
    '/servico/cadastro' => [ServicoController::class, 'cadastro'],
    '/servico/cadastrar' => [ServicoController::class, 'cadastrar'],
    
    // Adicione outras rotas aqui
];
