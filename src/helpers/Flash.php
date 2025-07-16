<?php
namespace Vennizlab\Agendaki\helpers;

class Flash
{
    public static function set($key, $message)
    {
        $_SESSION['flash'][$key] = $message;
    }

    public static function get($key)
    {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }

    public static function has($key)
    {
        return isset($_SESSION['flash'][$key]);
    }
}
