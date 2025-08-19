<?php

namespace Vennizlab\Agendaki\helpers;

use Vennizlab\Agendaki\core\Auth;

class MenuDTO
{
    private $subMenu = [];
    private $nome = "";
    private $url = "";
    private $permissoes = [];

    public function __construct( $url, $nome, $menu = [], $permissoes = [] )
    {
        $this->url = $url;
        $this->nome = $nome;
        $this->subMenu = $menu;
        $this->permissoes = $permissoes;
    }

    public function toHTML( )
    {
        $html = "<li>
                    <a href='$this->url'>$this->nome</a>";

        if( !empty( $this->subMenu ) )
        {
            $html .= "<ul>";

            foreach( $this->subMenu as $menu )
                $html .= $menu->toHTML( );

            $html .= "</ul>";
        }

        $html .= "</li>";

        return $html;
    }
}

class Menu{

    private $funcionario = [];
    private $usuario = [];

    public function __construct( )
    {
        $this->funcionario = [
            new MenuDTO( "/funcionario", "Funcionário", [
                new MenuDTO( "/funcionario/cadastro", "Cadastrar Funcionário" )
            ] ),
            new MenuDTO( "/servico", "Serviço", [
                new MenuDTO( "/servico/cadastro", "Cadastrar Serviço" )
            ] ),
            new MenuDTO( "/agenda", "Agenda", [
                new MenuDTO( "/agenda/cadastro", "Cadastrar Agenda" ),
                new MenuDTO( "/agendamento/cadastro", "Cadastrar Agendamento" )
            ] ),
            new MenuDTO( "/agendamento", "Agendamento", [
                new MenuDTO( "/agendamento/cadastro", "Cadastrar Agendamento" )
            ] ),
        ];

        $this->usuario = [
            new MenuDTO( "/", "Inicio" )
        ];
    }

    public function toHTML( )
    {
        $rotas = [];

        if( Auth::isFuncionario( ) )
            $rotas = $this->funcionario;
        else
            $rotas = $this->usuario;

        $html = "<nav>
                    <ul>";

        foreach( $rotas as $rota )
            $html .= $rota->toHTML( );
        
        $html .= "      <li class='logout'>
                            <a href='/auth/logout' class='logout-link'>Logout</a>
                        </li>
                    </ul>
                  </nav>";

        return $html;
    }
}