<?php

namespace Vennizlab\Agendaki\helpers;

use DateTime;
use Vennizlab\Agendaki\core\Auth;
use Vennizlab\Agendaki\core\Retorno;

class ValidacaoHelper{

    private array $validacao = [];

    public function validaHorario( $mensagem, $horario )
    {
        if( !preg_match("/^(?:[01]\d|2[0-3]):[0-5]\d$/", $horario) )
        {
            $this->addErro( $mensagem );
            return true;
        }
        
        return false;
    }

    function horarioEntre($hora, $inicio, $fim) {
        $str_hora   = strtotime($hora);
        $str_inicio = strtotime($inicio);
        $str_fim    = strtotime($fim);

        if( $str_hora >= $str_inicio && $str_hora <= $str_fim )
            return false;

        $this->addErro( "$hora está fora do horário permitido: $inicio - $fim" );
        return true;
    }

    public function nulo( $mensagem, $obj )
    {
        if( !isset($obj) )
        {
            $this->addErro( $mensagem );
            return true;
        }

        return false;
    }

    public function vazio( $mensagem, $obj )
    {
        if( empty($obj) )
        {
            $this->addErro( $mensagem );
            return true;
        }

        return false;
    }

    public function data( $mensagem, $data )
    {
        $d = DateTime::createFromFormat('Y-m-d', $data);

        if(!$d || $d->format('Y-m-d') !== $data )
        {
            $this->addErro( $mensagem );
            return true;
        }

        return false;
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

    public function permissao( $mensagem, array $permissao )
    {
        if( !Auth::permissao($permissao) )
        {
            $this->addErro( $mensagem );
            return true;
        }

        return false;
    }

    public function existe( $mensagem, array $comparacao, $obj )
    {
        if( in_array( $obj, $comparacao ) )
        {
            $this->addErro( $mensagem );
            return true;
        }

        return false;
    }

    public function naoExiste( $mensagem, array $comparacao, $obj )
    {
        if( !in_array( $obj, $comparacao ) )
        {
            $this->addErro( $mensagem );
            return true;
        }

        return false;
    }

    public function erroRetorno( Retorno $retorno )
    {
        if( !$retorno->is(Retorno::SUCESSO) )
        {
            $this->addErro( $retorno->getMensagem( ) );
            return true;
        }

        return false;
    }

    public function retorno( )
    {
        return new Retorno( Retorno::ERRO_VALIDACAO, ["erros" => $this->getValidacao( ) ] );
    }
}