<?php

namespace rest;

use classDbHandler\user\UserDBHandler;
use classDbHandler\user\UserTokenDBHandler;
use classModel\RequestParameters;
use classModel\User;
use classModel\UserToken;
use controller\UserController;
use controller\UserTokenController;
use exception\HttpResponseTriggerException;
use helper\VariableHelper;
use service\SessionHandler;

class UserHandler
{
    private UserController $userController;
    private UserTokenController $userTokenController;

    public function __construct()
    {
        $this->userController = new UserController();
        $this->userTokenController = new UserTokenController();
    }

    public function registerUser(RequestParameters $parameters)
    {
        $regData = $parameters->getRequestData();
        [$success, $resultData] = $this->userController->registerUser($regData);
        throw new HttpResponseTriggerException($success, $resultData);
    }

    public function loginUser(RequestParameters $parameters)
    {
        $loginData = $parameters->getRequestData();
        [$success, $user] = $this->userController->loginUser($loginData);
        if (!$success) {
            throw new HttpResponseTriggerException(false, $user);
        }

        $token = $this->userTokenController->generateAndSaveTokenFromUser($user);

        throw new HttpResponseTriggerException(true, ['token' => $token->getToken(), 'userData' => VariableHelper::convertObjectToArray($user)]);

    }

    public function logOutUser()
    {

        SessionHandler::getInstance();
//        var_dump($_SESSION);
        $token = $this->userTokenController->getTokenFromSession();

        $this->userTokenController->removeToken($token);
        throw new HttpResponseTriggerException(true, []);

    }

    public function getUserByToken(RequestParameters $requestParameters)
    {
        $token = $requestParameters->getUrlParameters()[0];
        $tokenObj = $this->userTokenController->getTokenObjectByString($token);
        $user = $this->userController->getAUserById($tokenObj->getUserId());
        throw new HttpResponseTriggerException(true, ['userData' => VariableHelper::convertObjectToArray($user)]);
    }

}
