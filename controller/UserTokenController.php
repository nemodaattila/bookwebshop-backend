<?php

namespace controller;

use classDbHandler\user\UserTokenDBHandler;
use classModel\User;
use classModel\UserToken;
use exception\HttpResponseTriggerException;
use helper\VariableHelper;
use service\SessionHandler;

class UserTokenController
{
    private UserTokenDBHandler $DBHandler;

    public function __construct()
    {
        $this->DBHandler = new UserTokenDBHandler();
    }

    public function generateAndSaveTokenFromUser(User $user): UserToken
    {
        $token = $this->generateToken($user);
        $token->setUserId($user->getId());
        $this->saveToken($token);
        return $token;
    }

    private function generateToken(User $userObj): UserToken
    {
        $token = new UserToken();
        $timeStamp = (new \DateTime())->getTimestamp() + 600;
        $hash = bin2hex(mhash(MHASH_SHA384, $timeStamp . $userObj->getUserName() . $userObj->getPassword() . $userObj->getEmail()));

        if (!$hash) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'UTGE']);
        }
        $token->setToken($hash);
        $token->setAuthorizationLevel($userObj->getAuthorizationLevel());
        $token->setExpirationTime($timeStamp);
        return $token;
    }

    private function saveToken(UserToken $token)
    {
        $this->saveTokenToDatabase($token);
        $this->saveTokenToSession($token);
    }

    private function saveTokenToDatabase(UserToken $token)
    {
        $result = $this->DBHandler->crate($token);
        if (!$result) throw new HttpResponseTriggerException(false, ['errorCode' => 'UTSE']);
    }

    private function saveTokenToSession(UserToken $token)
    {
        $sh = SessionHandler::getInstance();
        $sh::save('token', $token, true);
    }

    public function getTokenFromSession()
    {
        $sh = SessionHandler::getInstance();
        return $sh::read('token', true);
    }

    private function removeTokenFromSession()
    {
        $sh = SessionHandler::getInstance();
        $sh::delete('token');
    }

    public function getTokenObjectByString(string $tokenString): UserToken
    {
        $tokenObj = $this->getTokenFromSession() ?? $this->DBHandler->select($tokenString);
        if ($tokenObj === null) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'UTINULL']);

        }
//        var_dump($tokenObj);
        $this->checkActiveToken($tokenObj);

        return $tokenObj;
    }

    private function checkActiveToken(UserToken $tokenObj)
    {
        if (!$this->DBHandler->checkTokenIsActive($tokenObj)) {
            $this->removeToken($tokenObj);
            throw new HttpResponseTriggerException(false, ['errorCode' => 'UTEXP']);
        }
    }

    public function removeToken(UserToken $tokenObj)
    {
        $this->DBHandler->delete($tokenObj);
        $this->removeTokenFromSession();
    }

}
