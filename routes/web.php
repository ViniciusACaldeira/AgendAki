<?php

use Vennizlab\Agendaki\controllers\AgendaController;
use Vennizlab\Agendaki\controllers\AgendaControllerAPI;
use Vennizlab\Agendaki\controllers\AgendamentoController;
use Vennizlab\Agendaki\controllers\AgendamentoControllerAPI;
use Vennizlab\Agendaki\controllers\AuthControllerAPI;
use Vennizlab\Agendaki\controllers\CoreController;
use Vennizlab\Agendaki\controllers\FuncionarioController;
use Vennizlab\Agendaki\controllers\FuncionarioControllerAPI;
use Vennizlab\Agendaki\controllers\HomeController;
use Vennizlab\Agendaki\controllers\PermissaoControllerAPI;
use Vennizlab\Agendaki\controllers\UsuarioController;
use Vennizlab\Agendaki\controllers\ServicoController;
use Vennizlab\Agendaki\controllers\ServicoControllerAPI;
use Vennizlab\Agendaki\controllers\UsuarioControllerAPI;
use Vennizlab\Agendaki\helpers\Permissoes;
use Vennizlab\Agendaki\middlewares\PermissaoMiddleware;

// Lista de rotas: 'url' => [Classe, 'método']
return [
    '/usuario/index' => [UsuarioController::class, 'index'],
    '/auth/login' => [UsuarioController::class, 'login'],
    '/auth/logout' => [UsuarioController::class, 'logout'],
    '/auth/cadastrar' => [UsuarioController::class, 'cadastrar'],
    '/dashboard' => [HomeController::class, 'dashboard'],
    '/notFound' => [CoreController::class, 'naoEncontrada' ],
    
    '/funcionario' => [FuncionarioController::class, 'index'],
    '/funcionario/cadastro' => [FuncionarioController::class, 'cadastro'],
    '/funcionario/cadastrar' => [FuncionarioController::class, 'cadastrar'],
    '/funcionario/detalhe' => [FuncionarioController::class, 'detalhe'],
    '/funcionario/atualizarServico' => [FuncionarioController::class, 'atualizaServico'],
    
    '/servico' => [ServicoController::class, 'index'],
    '/servico/cadastro' => [ServicoController::class, 'cadastro'],
    '/servico/detalhe' => [ServicoController::class, 'detalhe'],
    
    '/agenda' => [AgendaController::class, 'index'],
    '/agenda/cadastro' => [AgendaController::class, 'cadastro'],
    '/agenda/cadastrar' => [AgendaController::class, 'cadastrar'],
    '/agenda/listar' => [AgendaController::class, 'listar'],

    '/agendamento' => [AgendamentoController::class, 'index'],
    '/agendamento/cadastro' => [AgendamentoController::class, 'cadastro'],
    '/agendamento/cadastrar' => [AgendamentoController::class, 'cadastrar'],

    '' => [HomeController::class, "index" ],
    // Adicione outras rotas aqui

    '/api/agenda' => [AgendaControllerAPI::class, "listar"],
    '/api/agenda/servico' => [AgendaControllerAPI::class, 'getServicos'],
    '/api/agenda/cadastrar' => [AgendaControllerAPI::class, "cadastrar"],
    '/api/agenda/tipo' => [AgendaControllerAPI::class, 'listarTipos'],
    
    '/api/agendamento/servico/disponivel' => [AgendamentoControllerAPI::class, 'servicosDisponiveis'],
    '/api/agendamento' => [AgendamentoControllerAPI::class, 'listar'],
    '/api/agendamento/cadastrar' => [AgendamentoControllerAPI::class, 'cadastrar'],

    '/api/servico/funcionario' => [ServicoControllerAPI::class, 'servicoByFuncionario'],
    '/api/servico/funcionario/cadastrar' => [ServicoControllerAPI::class, 'cadastrarServicoFuncionario'],
    '/api/servico' => [ServicoControllerAPI::class, "getServicos"],
    '/api/servico/cadastrar' => [ServicoControllerAPI::class, "cadastrar"],
    '/api/servico/preco' => [ServicoControllerAPI::class, "getPreco"],
    '/api/servico/preco/cadastrar' => [ServicoControllerAPI::class, "cadastrarPreco"],
    '/api/servico/preco/editar' => [ServicoControllerAPI::class, 'editarPreco'],
    '/api/servico/editar' => [ServicoControllerAPI::class, 'editar'],
    '/api/servico/inativar' => [ServicoControllerAPI::class, 'inativar'],

    '/api/funcionario' => [FuncionarioControllerAPI::class, 'listar'],
    '/api/funcionario/cadastrar' => [FuncionarioControllerAPI::class, 'cadastrar'],
    
    '/api/cliente' => [UsuarioControllerAPI::class, 'listarClientes'],

    '/api/permissoes' => [PermissaoControllerAPI::class, "listar"],
    '/api/permissoes/vincular' => [PermissaoControllerAPI::class, "vincular"],
    '/api/permissoes/funcionario' => [PermissaoControllerAPI::class, "listarFuncionario", [PermissaoMiddleware::class, [Permissoes::CONSULTA_FUNCIONARIO]]],

    '/api/auth/login' => [AuthControllerAPI::class, "login"],
    '/api/auth/cadastrar' => [AuthControllerAPI::class, "cadastrar" ],

];
