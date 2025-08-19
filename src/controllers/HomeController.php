<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Controller;

class HomeController extends Controller{

    public function dashboard( )
    {
        $this->view("dashboard");
    }

    public function index( )
    {
        $this->view( "cliente/index" );
    }
}