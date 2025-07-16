<?php

namespace Vennizlab\Agendaki\core;

class Controller{
    public function view($view, $data = [] )
    {
        extract($data);

        include "../src/views/{$view}.php";
    }

    public function redirect($url, $data = [])
    {
        extract($data);

        header("Location: /{$url}");
        exit;
    }
}