<?php

namespace Vennizlab\Agendaki\core;

use Vennizlab\Agendaki\helpers\Permissoes;

class Auth{
    private static $usuario;

    public static function setUsuario( $usuario )
    {
        self::$usuario = $usuario;
    }

    public static function usuario( )
    {
        return self::$usuario;
    }

    public static function permissao( array $permissao )
    {
        $permissoesUsuario = self::usuario( )->permissoes ?? null;

        if( !in_array( Permissoes::ADMINISTRADOR, $permissoesUsuario ) )
            if( !$permissoesUsuario || !in_array($permissoesUsuario, $permissao))
                return false;

        return true;
    }
}