<?php

namespace service;

use classModel\UserToken;
use controller\UserTokenController;

class Authentication
{
    private static ?Authentication $instance = null;

    private static array $tokenState = [false, ''];

    public static function getInstance(): Authentication
    {
        if (self::$instance === null) {
            self::$instance = new Authentication();
        }
        return self::$instance;
    }

    public function authenticateUserByToken(string $token)
    {
        $tokenCont = new UserTokenController();
        self::$tokenState = $tokenCont->getTokenObjectByString($token);
    }

    /**
     * @return array
     */
    public function getTokenState(): array
    {
        return self::$tokenState;
    }

    public function getTokenObj(): UserToken
    {
        $tokenCont = new UserTokenController();
        return $tokenCont->getTokenFromSession();
    }

}
