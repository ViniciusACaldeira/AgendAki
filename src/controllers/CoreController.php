<?php

namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Controller;

class CoreController extends Controller{
    public function naoEncontrada( )
    {
        return $this->renderNaoEncontrada( );
    }
}