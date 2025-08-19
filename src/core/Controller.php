<?php

namespace Vennizlab\Agendaki\core;

use Vennizlab\Agendaki\helpers\Paginacao;

class Controller{
    private ?Paginacao $paginacao = null;

    public function renderNaoEncontrada( )
    {
        http_response_code( 404 );
        $this->render( "naoEncontrada" );
        exit;
    }

    public function render( $view, $data = [] )
    {
        extract( $data );

        include "../src/views/{$view}.php";
    }

    public function view($view, $data = [] )
    {
        extract($data);
        
        ob_start();
        include "../src/views/{$view}.php";
        $conteudo = ob_get_clean();

        include "../src/views/layout.php";
    }

    public function redirect($url, $data = [])
    {
        extract($data);

        header("Location: /{$url}");
        exit;
    }

    public function response( $status, $data )
    {
        $data = [ "status" => $status, "data" => $data ];

        if( $this->paginacao != null )
            if( $this->paginacao->ePaginado( ) )
                $data["paginacao"] = $this->paginacao->response( );

        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode( $data );
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

    public function temCampo( string $campo )
    {
        switch( $this->getTipo( ) )
        {
            case "GET":
                return isset($_GET[$campo]);
                break;
            case "POST":
                return isset($_POST[$campo]);
                break;
            default:
                return false;
        }
    }

    public function responseRetorno( Retorno $retorno )
    {
        return $this->response( $retorno->getStatusHTTP(), $retorno->getMensagem());
    }

    public function responseNaoEncontrado( )
    {
        return $this->response( 405, [ "data" => [], "mensagem" => "Método não permitido!" ] );
    }

    public function getPaginacao( )
    {
        if( $this->paginacao == null )
        {
            $paginacao = new Paginacao( );
            $paginacao->init( );

            $this->paginacao = $paginacao;
        }

        return $this->paginacao;
    }
}