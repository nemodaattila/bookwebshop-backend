<?php

namespace rest;

use classModel\RequestParameters;
use controller\UserController;
use controller\UserTokenController;
use exception\HttpResponseTriggerException;
use helper\VariableHelper;
use service\Authentication;
use service\SessionHandler;

/**
 * Class UserHandler http request handler class , for users (registration, login, etc.)
 * @package rest
 */
class UserHandler
{
    private UserController $userController;
    private UserTokenController $userTokenController;

    public function __construct()
    {
        $this->userController = new UserController();
        $this->userTokenController = new UserTokenController();
    }

    /**
     * registers a user
     * @param RequestParameters $parameters http request parameters
     * @throws HttpResponseTriggerException throws result in exception, db errors
     */
    public function registerUser(RequestParameters $parameters)
    {
        $regData = $parameters->getRequestData();
        [$success, $resultData] = $this->userController->registerUser($regData);
        throw new HttpResponseTriggerException($success, $resultData);
    }

    /**
     * logs a user in
     * result is token + userdata
     * @param RequestParameters $parameters http request parameters
     * @throws HttpResponseTriggerException throws result in exception, db errors
     */
    public function loginUser(RequestParameters $parameters)
    {
        $loginData = $parameters->getRequestData();
        [$success, $user] = $this->userController->loginUser($loginData);
        if (!$success) {
            throw new HttpResponseTriggerException(false, $user);
        }
        $token = $this->userTokenController->generateAndSaveTokenFromUser($user);
        (Authentication::getInstance())->setTokenState();
        throw new HttpResponseTriggerException(true, ['token' => $token->getToken(), 'userData' => VariableHelper::convertObjectToArray($user)]);
    }

    /**
     * logs out user stored in session/database (by token)
     * @throws HttpResponseTriggerException throws result as exception, db errors
     */
    public function logOutUser()
    {
        SessionHandler::getInstance();
//        var_dump($_SESSION);
        $token = $this->userTokenController->getTokenFromSession();
        $this->userTokenController->removeToken($token);
        (Authentication::getInstance())->resetTokenState();
        throw new HttpResponseTriggerException(true, []);
    }

    /**
     * returns a user based on token string
     * result is [<bool> success, <array> userdata]
     * @throws HttpResponseTriggerException result data or error data
     */
    public function getUserByToken()
    {

        [$hasUser, $message] = (Authentication::getInstance())->getTokenState();

        if (!$hasUser) {
            throw new HttpResponseTriggerException(false, ['errorCode' => $message]);
        } else
            $userId = (Authentication::getInstance())->getTokenObj()->getUserId();
        $user = $this->userController->getAUserById($userId);
        $user->setPassword('');
        throw new HttpResponseTriggerException(true, ['userData' => VariableHelper::convertObjectToArray($user)]);

//        $token = $requestParameters->getUrlParameters()[0];
//        $tokenObj = $this->userTokenController->getTokenObjectByString($token);
//        $user = $this->userController->getAUserById($tokenObj->getUserId());
//        $user->setPassword('');
//        throw new HttpResponseTriggerException(true, ['userData' => VariableHelper::convertObjectToArray($user), 'tokenExpires'=>$tokenObj->getExpirationTime()]);
    }

}
