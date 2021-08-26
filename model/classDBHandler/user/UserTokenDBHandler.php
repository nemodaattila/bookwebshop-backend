<?php

namespace classDbHandler\user;

use classDbHandler\DBHandlerParent;
use classModel\UserToken;
use DateTime;
use exception\HttpResponseTriggerException;

/**
 * Class UserTokenDBHandler database connection class for User Tokens (user_token table)
 * @package classDbHandler\user
 */
class UserTokenDBHandler extends DBHandlerParent
{
    /**
     * inserts a token record into table
     * @param UserToken $user token Object
     * @return bool creation is successful
     * @throws HttpResponseTriggerException wrong processor type, query error
     */
    public function create(UserToken $user): bool
    {
        $this->createPDO('insert');
        $this->PDOLink->setCommand('INSERT INTO user_token (token, user_id, authorization_level, expiration_time) values (?,?,?,?)');
        $this->PDOLink->setValues([$user->getToken(), $user->getUserId(), $user->getAuthorizationLevel(), $user->getExpirationTime()]);
        return $this->PDOLink->execute();
    }

    /**
     * returns a token record based on token string
     * @param string $token token string
     * @return UserToken|null Token object if token valid, else null
     * @throws HttpResponseTriggerException processor type, query error
     */
    public function select(string $token): ?UserToken
    {
        $userToken = new UserToken();
        $this->createPDO('select');
        $this->PDOLink->setCommand('select ut.token, ut.user_id, ut.authorization_level, ut.expiration_time from user_token as ut where ut.token = ?');
        $this->PDOLink->setValues($token);
        $result = $this->PDOLink->execute();
        if (count($result) !== 1) {
            return null;
        }
        $result = $result[0];
        $userToken->setToken($result['token']);
        $userToken->setUserId($result['user_id']);
        $userToken->setExpirationTime($result['expiration_time']);
        $userToken->setAuthorizationLevel($result['authorization_level']);
        return $userToken;
    }

    /**
     * refreshes the expiration time of a token in the database
     * @param UserToken $tokenObj token Object
     * @return bool operation success
     * @throws HttpResponseTriggerException database errors
     */
    public function refreshExpirationDate(UserToken $tokenObj): bool
    {
        $this->createPDO('update');
        $this->PDOLink->setCommand('update ut.token, ut.user_id, ut.authorization_level, ut.expiration_time from user_token as ut where ut.token = ?');
        $this->PDOLink->setCommand('UPDATE user_token SET 	expiration_time = ? WHERE token=?');
        $this->PDOLink->setValues([$tokenObj->getExpirationTime(), $tokenObj->getToken()]);
        return $this->PDOLink->execute();
    }

    /**
     * checks if token is active i.e. expiration time not older than 10 minutes
     * @param UserToken $token token Object
     * @return bool activity as boolean
     */
    public function checkTokenIsActive(UserToken $token): bool
    {
        return ($token->getExpirationTime() - (new DateTime())->getTimestamp() > 0);
    }

    /**
     * removes a token record from table
     * @param UserToken $token token Object
     * @return bool delete succeeded
     * @throws HttpResponseTriggerException wrong processor type , query error
     */
    public function delete(UserToken $token): bool
    {
        $this->createPDO('delete');
        $this->PDOLink->setCommand('DELETE FROM user_token as ut where ut.user_id = ?');
        $this->PDOLink->setValues([$token->getUserId()]);
        return $this->PDOLink->execute();
    }
}
