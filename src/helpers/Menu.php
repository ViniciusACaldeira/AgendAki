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
        $temSubmenu = !empty( $this->subMenu );

        $classeSubmenu = $temSubmenu ? "class='has-submenu'"  : "";
        $arrow = $temSubmenu ? "<span class='arrow'>▾</span>" : "";

        $html = "<li $classeSubmenu>
                    <a href='$this->url'>$this->nome$arrow</a>";

        if( $temSubmenu )
        {
            $html .= "<ul class='submenu'>";

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
            new MenuDTO( "/perfil", "Meu Perfil" )
        ];

        $this->usuario = [
            new MenuDTO( "/", "Inicio" ),
            new MenuDTO( "/perfil", "Meu Perfil" )
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
                    <div class='nav-container'>
                        <div class='hamburger' id='hamburger'>☰</div>
                        <ul id='nav-menu'>";

        foreach( $rotas as $rota )
            $html .= $rota->toHTML( );
        
        $html .= "          <li class='logout'>
                                <a href='/auth/logout' class='logout-link'>Logout</a>
                            </li>
                        </ul>
                    </div>
                  </nav>";

        return $html;
    }
}