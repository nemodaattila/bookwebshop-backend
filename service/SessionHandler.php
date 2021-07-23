<?php

namespace service;

class SessionHandler
{
    private static ?SessionHandler $instance = null;

    public static function getInstance(): SessionHandler
    {
        if (self::$instance === null) {
            self::$instance = new SessionHandler();
        }
        return self::$instance;
    }

    public function __construct()
    {
        if (!isset($_SESSION)) {
            session_start(['cookie_httponly' => false]);
        }
    }

    public static function save(string $name, mixed $value, bool $serialize = false)
    {
        if ($serialize)
        {
            $value = serialize($value);
        }
        $_SESSION[$name] = $value;
    }

    public static function read(string $name, bool $unSerialize = false)
    {
//        var_dump($_SESSION);

        if (!(isset($_SESSION[$name])))
            return null;
        if ($unSerialize)
        {
            return unserialize($_SESSION[$name]);
        }
        return $_SESSION[$name];
    }

    public static function delete(string $name)
    {
        unset($_SESSION[$name]);
    }

//    public function __destruct()
//    {
//        session_destroy();
//        // TODO: Implement __destruct() method.
//    }
}
