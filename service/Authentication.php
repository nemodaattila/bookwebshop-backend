<?php

namespace service;

use classModel\UserToken;
use controller\UserTokenController;

/**
 * class for handling user authentication
 * Class Authentication
 * @package service
 */
class Authentication
{
    /**
     * @var Authentication|null singleton instance
     */
    private static ?Authentication $instance = null;

    /**
     * @var array result of authentication check [isAuthenticated: bool, errorCode: string]
     */
    private static array $tokenState = [false, ''];

    public static function getInstance(): Authentication
    {
        if (self::$instance === null) {
            self::$instance = new Authentication();
        }
        return self::$instance;
    }

    /**
     * checks if authorization token is valid
     * @param string $token
     */
    public function authenticateUserByToken(string $token)
    {
        $tokenCont = new UserTokenController();
        self::$tokenState = $tokenCont->getTokenObjectByString($token);
    }

    /**
     * returns result of authentication check [isAuthenticated: bool, errorCode: string]
     * @return array
     */
    public function getTokenState(): array
    {
        return self::$tokenState;
    }

    /**
     * returns the token object of the current logged user
     * @return UserToken
     */
    public function getTokenObj(): UserToken
    {
        $tokenCont = new UserTokenController();
        return $tokenCont->getTokenFromSession();
    }

}
