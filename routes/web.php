<?php

use Vennizlab\Agendaki\controllers\AgendaController;
use Vennizlab\Agendaki\controllers\AgendaControllerAPI;
use Vennizlab\Agendaki\controllers\AgendamentoController;
use Vennizlab\Agendaki\controllers\AgendamentoControllerAPI;
use Vennizlab\Agendaki\controllers\AuthControllerAPI;
use Vennizlab\Agendaki\controllers\FuncionarioController;
use Vennizlab\Agendaki\controllers\FuncionarioControllerAPI;
use Vennizlab\Agendaki\controllers\HomeController;
use Vennizlab\Agendaki\controllers\PermissaoControllerAPI;
use Vennizlab\Agendaki\controllers\UsuarioController;
use Vennizlab\Agendaki\controllers\ServicoController;
use Vennizlab\Agendaki\controllers\ServicoControllerAPI;
use Vennizlab\Agendaki\controllers\UsuarioControllerAPI;
use Vennizlab\Agendaki\helpers\Permissoes;
use Vennizlab\Agendaki\middlewares\AuthMiddleware;
use Vennizlab\Agendaki\middlewares\PermissaoMiddleware;

// Lista de rotas: 'url' => [Classe, 'método']
return [
    '/usuario/index' => [UsuarioController::class, 'index'],
    '/auth/login' => [UsuarioController::class, 'login'],
    '/auth/logout' => [UsuarioController::class, 'logout'],
    '/auth/cadastrar' => [UsuarioController::class, 'cadastrar'],
    '/dashboard' => [HomeController::class, 'dashboard', [[AuthMiddleware::class, 'web']]],
    
    '/funcionario' => [FuncionarioController::class, 'index', [[AuthMiddleware::class, 'web']]],
    '/funcionario/cadastro' => [FuncionarioController::class, 'cadastro', [[AuthMiddleware::class, 'web']]],
    '/funcionario/cadastrar' => [FuncionarioController::class, 'cadastrar', [[AuthMiddleware::class, 'web']]],
    '/funcionario/detalhe' => [FuncionarioController::class, 'detalhe', [[AuthMiddleware::class, 'web']]],
    '/funcionario/atualizarServico' => [FuncionarioController::class, 'atualizaServico', [[AuthMiddleware::class, 'web']]],
    
    '/servico' => [ServicoController::class, 'index', [[AuthMiddleware::class, 'web']]],
    '/servico/cadastro' => [ServicoController::class, 'cadastro', [[AuthMiddleware::class, 'web']]],
    '/servico/detalhe' => [ServicoController::class, 'detalhe', [[AuthMiddleware::class, 'web']]],
    
    '/agenda' => [AgendaController::class, 'index', [[AuthMiddleware::class, 'web']]],
    '/agenda/cadastro' => [AgendaController::class, 'cadastro', [[AuthMiddleware::class, 'web']]],
    '/agenda/cadastrar' => [AgendaController::class, 'cadastrar', [[AuthMiddleware::class, 'web']]],
    '/agenda/listar' => [AgendaController::class, 'listar', [[AuthMiddleware::class, 'web']]],

    '/agendamento' => [AgendamentoController::class, 'index', [[AuthMiddleware::class, 'web']]],
    '/agendamento/cadastro' => [AgendamentoController::class, 'cadastro', [[AuthMiddleware::class, 'web']]],
    '/agendamento/cadastrar' => [AgendamentoController::class, 'cadastrar', [[AuthMiddleware::class, 'web']]],
    // Adicione outras rotas aqui

    '/api/agenda' => [AgendaControllerAPI::class, "listar"],
    '/api/agenda/servico' => [AgendaControllerAPI::class, 'getServicos'],
    '/api/agenda/cadastrar' => [AgendaControllerAPI::class, "cadastrar", [AuthMiddleware::class]],

    '/api/agendamento/servico/disponivel' => [AgendamentoControllerAPI::class, 'servicosDisponiveis'],
    '/api/agendamento' => [AgendamentoControllerAPI::class, 'listar'],
    '/api/agendamento/cadastrar' => [AgendamentoControllerAPI::class, 'cadastrar'],

    '/api/servico/funcionario' => [ServicoControllerAPI::class, 'servicoByFuncionario'],
    '/api/servico/funcionario/cadastrar' => [ServicoControllerAPI::class, 'cadastrarServicoFuncionario', [AuthMiddleware::class]],
    '/api/servico' => [ServicoControllerAPI::class, "getServicos"],
    '/api/servico/cadastrar' => [ServicoControllerAPI::class, "cadastrar"],
    '/api/servico/preco' => [ServicoControllerAPI::class, "getPreco"],
    '/api/servico/preco/cadastrar' => [ServicoControllerAPI::class, "cadastrarPreco", [AuthMiddleware::class]],
    '/api/servico/preco/editar' => [ServicoControllerAPI::class, 'editarPreco', [AuthMiddleware::class]],
    '/api/servico/editar' => [ServicoControllerAPI::class, 'editar', [AuthMiddleware::class]],
    '/api/servico/inativar' => [ServicoControllerAPI::class, 'inativar', [AuthMiddleware::class]],
    
    '/api/funcionario' => [FuncionarioControllerAPI::class, 'listar', [AuthMiddleware::class]],

    '/api/usuario/cliente' => [UsuarioControllerAPI::class, 'listarClientes', [AuthMiddleware::class]],

    '/api/permissoes' => [PermissaoControllerAPI::class, "listar", [AuthMiddleware::class]],
    '/api/permissoes/vincular' => [PermissaoControllerAPI::class, "vincular", [AuthMiddleware::class]],
    '/api/permissoes/funcionario' => [PermissaoControllerAPI::class, "listarFuncionario", [AuthMiddleware::class, [PermissaoMiddleware::class, [Permissoes::CONSULTA_FUNCIONARIO]]]],

    '/api/auth/login' => [AuthControllerAPI::class, "login"],
];
