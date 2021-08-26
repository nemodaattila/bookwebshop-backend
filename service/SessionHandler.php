<?php

namespace service;

/**
 * Class SessionHandler helper class for handling sessions
 * @package service
 */
class SessionHandler
{
    /**
     * @var SessionHandler|null static instance
     */
    private static ?SessionHandler $instance = null;

    public function __construct()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    public static function getInstance(): SessionHandler
    {
        if (self::$instance === null) {
            self::$instance = new SessionHandler();
        }
        return self::$instance;
    }

    /**
     * sets a value in session
     * @param string $name key name
     * @param mixed $value value to be saved
     * @param bool $serialize if true serializes the value
     */
    public static function set(string $name, mixed $value, bool $serialize = false)
    {
        if ($serialize) {
            $value = serialize($value);
        }
        $_SESSION[$name] = $value;
    }

    /**
     * gets a value from session
     * @param string $name key name to get
     * @param bool $unSerialize if true un-serializes the value
     */
    public static function get(string $name, bool $unSerialize = false): mixed
    {
        if (!(isset($_SESSION[$name])))
            return null;
        if ($unSerialize) {
            return unserialize($_SESSION[$name]);
        }
        return $_SESSION[$name];
    }

    /**
     * removes a value from Session
     * @param string $name key of value to be removed
     */
    public static function unset(string $name)
    {
        unset($_SESSION[$name]);
    }
}
