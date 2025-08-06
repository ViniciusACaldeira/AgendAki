<?php

namespace Vennizlab\Agendaki\helpers;

use DateTime;

class ValidacaoHelper{

    private array $validacao = [];

    public function validaHorario( $mensagem, $horario )
    {
        if( !preg_match("/^(?:[01]\d|2[0-3]):[0-5]\d$/", $horario) )
            $this->addErro( $mensagem );
    }

    public function nulo( $mensagem, $obj )
    {
        if( !isset($obj) )
            $this->addErro( $mensagem );
    }

    public function vazio( $mensagem, $obj )
    {
        if( empty($obj) )
            $this->addErro( $mensagem );
    }

    public function data( $mensagem, $data )
    {
        $d = DateTime::createFromFormat('Y-m-d', $data);

        if(!$d || $d->format('Y-m-d') !== $data )
            $this->addErro( $mensagem );
    }
    
    public function naoVazio( $mensagem, $obj )
    {
        if( !empty($obj) )
            $this->addErro($mensagem);
    }

    public function getValidacao( )
    {
        return $this->validacao;
    }

    public function addErro( $mensagem )
    {
        if( is_array($mensagem) )
            foreach( $mensagem as $m )
                $this->validacao[] = $m;
        else
            $this->validacao[] = $mensagem;
    }

    public function temErro( )
    {
        return !empty($this->validacao);
    }
}