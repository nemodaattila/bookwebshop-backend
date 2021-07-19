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
            session_start();
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
}
