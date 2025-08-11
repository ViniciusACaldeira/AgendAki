<?php

namespace Vennizlab\Agendaki\core;

class Retorno{
    public const SUCESSO = "sucesso";
    public const ERRO = "erro";
    public const ERRO_VALIDACAO = "erro_validacao";

    public $status;
    public array $mensagem = [];

    public function __construct( $status = "", $mensagem = [] )
    {
        $this->status = $status;

        if( is_array($mensagem) ) 
        {
            if(array_keys($mensagem) !== range(0, count($mensagem) - 1))
                $this->mensagem = $mensagem;
            else
                foreach ($mensagem as $m)
                    $this->mensagem[] = $m;
        }
        else 
            $this->mensagem[] = $mensagem;
    }

    public function getStatus( )
    {
        return $this->status;
    }

    public function getMensagem( )
    {
        return $this->mensagem;
    }

    public function is( $status )
    {
        if( !is_array($status))
            $status = [$status];            

        return in_array($this->getStatus(), $status, true);
    }

    public function getStatusHTTP( )
    {
        switch( $this->getStatus() )
        {
            case Retorno::SUCESSO:
                return 200;
            case Retorno::ERRO:
                return 500;
            case Retorno::ERRO_VALIDACAO:
                return 400;
            default:
                return 404;
        }
    }
}