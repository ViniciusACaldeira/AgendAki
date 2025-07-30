<?php

use Vennizlab\Agendaki\controllers\AgendaController;
use Vennizlab\Agendaki\controllers\AgendaControllerAPI;
use Vennizlab\Agendaki\controllers\AgendamentoController;
use Vennizlab\Agendaki\controllers\AgendamentoControllerAPI;
use Vennizlab\Agendaki\controllers\FuncionarioController;
use Vennizlab\Agendaki\controllers\HomeController;
use Vennizlab\Agendaki\controllers\UsuarioController;
use Vennizlab\Agendaki\controllers\ServicoController;
use Vennizlab\Agendaki\controllers\ServicoControllerAPI;

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
    '/funcionario/detalhe' => [FuncionarioController::class, 'detalhe'],
    '/funcionario/atualizarServico' => [FuncionarioController::class, 'atualizaServico'],
    
    '/servico' => [ServicoController::class, 'index'],
    '/servico/cadastro' => [ServicoController::class, 'cadastro'],
    '/servico/cadastrar' => [ServicoController::class, 'cadastrar'],
    
    '/agenda' => [AgendaController::class, 'index'],
    '/agenda/cadastro' => [AgendaController::class, 'cadastro'],
    '/agenda/cadastrar' => [AgendaController::class, 'cadastrar'],
    '/agenda/listar' => [AgendaController::class, 'listar'],

    '/agendamento' => [AgendamentoController::class, 'index'],
    '/agendamento/cadastro' => [AgendamentoController::class, 'cadastro'],
    '/agendamento/cadastrar' => [AgendamentoController::class, 'cadastrar'],
    // Adicione outras rotas aqui

    '/api/agenda/servico' => [AgendaControllerAPI::class, 'getServicos'],
    '/api/agendamento/servico/disponivel' => [AgendamentoControllerAPI::class, 'servicosDisponiveis'],
    '/api/servico/funcionario' => [ServicoControllerAPI::class, 'servicoByFuncionario'],
    '/api/servico/funcionario/cadastrar' => [ServicoControllerAPI::class, 'cadastrarServicoFuncionario'],
    '/api/servico' => [ServicoControllerAPI::class, "getServicos"],
    
];
