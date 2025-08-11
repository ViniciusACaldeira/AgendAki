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

    private string $tipo = "";

    public function isPOST( )
    {
        return $_SERVER['REQUEST_METHOD'] == "POST";
    }

    public function isGET( )
    {
        return $_SERVER['REQUEST_METHOD'] == "GET";
    }

    public function getTipo( )
    {
        if( $this->tipo == "" )
            $this->tipo = $_SERVER['REQUEST_METHOD'];

        return $this->tipo;
    }

    public function getCampo( string $campo, $default = null )
    {
        switch( $this->getTipo( ) )
        {
            case "GET":
                return $_GET[$campo] ?? $default;
                break;
            case "POST":
                return $_POST[$campo] ?? $default;
                break;
            default:
                return null;
        }
    }

    public function responseRetorno( Retorno $retorno )
    {
        return $this->response( $retorno->getStatusHTTP(), $retorno->getMensagem());
    }

    public function responseNaoEncontrado( )
    {
        return $this->response( 400, "Não encontrado!" );
    }
}