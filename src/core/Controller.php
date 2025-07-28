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

    public function response( $status, $data )
    {
        header('Content-Type: application/json');
        http_response_code($status); // Opcional: define o código HTTP de resposta
        echo json_encode([
            "status" => $status,
            "data" => $data
        ]);
        exit;
    }
}