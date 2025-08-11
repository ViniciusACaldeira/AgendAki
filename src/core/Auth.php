<?php

namespace Vennizlab\Agendaki\core;

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
}