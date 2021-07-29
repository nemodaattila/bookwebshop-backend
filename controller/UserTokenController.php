<?php

namespace controller;

use classDbHandler\user\UserTokenDBHandler;
use classModel\User;
use classModel\UserToken;
use DateTime;
use exception\HttpResponseTriggerException;
use service\SessionHandler;

/**
 * Class UserTokenController controller class for handling user authentication tokens
 * @package controller
 */
class UserTokenController
{
    /**
     * @var UserTokenDBHandler instance of UserTokenDBHandler
     */
    private UserTokenDBHandler $DBHandler;

    public function __construct()
    {
        $this->DBHandler = new UserTokenDBHandler();
    }

    /**
     * generates and saves token based on User Data
     * @param User $user user data
     * @return UserToken user token object
     * @throws HttpResponseTriggerException error during token generation, error during save
     */
    public function generateAndSaveTokenFromUser(User $user): UserToken
    {
        $token = $this->generateToken($user);
        $token->setUserId($user->getId());
        $this->saveToken($token);
        return $token;
    }

    /**
     * generates authorization token
     * @param User $userObj User data
     * @return UserToken user token object
     * @throws HttpResponseTriggerException
     */
    private function generateToken(User $userObj): UserToken
    {
        $token = new UserToken();
        $timeStamp = (new DateTime())->getTimestamp() + 600;
        $hash = bin2hex(mhash(MHASH_SHA384, $timeStamp . $userObj->getUserName() . $userObj->getPassword() . $userObj->getEmail()));

        if (!$hash) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'UTGE']);
        }
        $token->setToken($hash);
        $token->setAuthorizationLevel($userObj->getAuthorizationLevel());
        $token->setExpirationTime($timeStamp);
        return $token;
    }

    /**
     * saves token to session and database
     * @param UserToken $token user token object
     * @throws HttpResponseTriggerException save error
     */
    private function saveToken(UserToken $token)
    {
        $this->saveTokenToDatabase($token);
        $this->saveTokenToSession($token);
    }

    /**
     * saves token to database
     * @param UserToken $token token Object
     * @throws HttpResponseTriggerException save error
     */
    private function saveTokenToDatabase(UserToken $token)
    {
        $result = $this->DBHandler->create($token);
        if (!$result) throw new HttpResponseTriggerException(false, ['errorCode' => 'UTSE']);
    }

    /**
     * save token to session
     * @param UserToken $token token Object
     */
    private function saveTokenToSession(UserToken $token)
    {
        $sh = SessionHandler::getInstance();
        $sh::set('token', $token, true);
    }

    /**
     * reads user token from Session
     * @return UserToken|null
     */
    public function getTokenFromSession(): UserToken|null
    {
        $sh = SessionHandler::getInstance();
        return $sh::get('token', true);
    }

    /**
     * removes token from session
     */
    private function removeTokenFromSession()
    {
        $sh = SessionHandler::getInstance();
        $sh->unset('token');
    }

    /**
     * searches token in Session or Database by token string
     * if exists checks if it is active
     * if active returns token
     * @param string $tokenString
     * @return array token object
//     * @throws HttpResponseTriggerException if token not exists OR expired
     * TODO if token is active but not exists in Session , create
     */
    public function getTokenObjectByString(string $tokenString): array
    {
//        var_dump('sfsdf');
        $tokenObj = $this->getTokenFromSession();
        if ($tokenObj === null) {
            $tokenObjDB = $this->DBHandler->select($tokenString);
            if ($tokenObjDB === null) {
                return [false, 'UTNE'];
            } else {
                $tokenObj = $tokenObjDB;
                $this->saveTokenToSession($tokenObj);
            }
        }
        if (!$this->checkActiveToken($tokenObj)) {
            return [false, 'UTEXP'];
        }
        $this->refreshTokenExpirationDate();
        return [true, ''];

    }

    /**
     * checks if token is younger than 10 minutes, if not removes it
     * @param UserToken $tokenObj Token Object
     * @throws HttpResponseTriggerException database errors
     */
    private function checkActiveToken(UserToken $tokenObj): bool
    {
        if (!$this->DBHandler->checkTokenIsActive($tokenObj)) {
            $this->removeToken($tokenObj);
            return false;
        }
        return true;
    }

    /**
     * removes token form session and from database
     * @param UserToken $tokenObj token Object
     * @throws HttpResponseTriggerException user delete error
     */
    public function removeToken(UserToken $tokenObj)
    {
        $this->DBHandler->delete($tokenObj);
        $this->removeTokenFromSession();
    }

    private function refreshTokenExpirationDate()
    {
        $tokenObj = $this->getTokenFromSession();
        $timeStamp = (new DateTime())->getTimestamp() + 600;
        $tokenObj->setExpirationTime($timeStamp);
        $this->DBHandler->refreshExpirationDate($tokenObj);
        $this->saveTokenToSession($tokenObj);
    }

}
