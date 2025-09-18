<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Auth;
use Vennizlab\Agendaki\core\Controller;

class HomeController extends Controller{

    public function dashboard( )
    {
        $this->view("dashboard");
    }

    public function index( )
    {
        if( Auth::isFuncionario( ) )
            return $this->redirect( "dashboard" );

        $this->view( "cliente/index" );
    }
}