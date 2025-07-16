<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Controller;

class HomeController extends Controller{

    public function dashboard( )
    {
        $this->view("dashboard");
    }
}