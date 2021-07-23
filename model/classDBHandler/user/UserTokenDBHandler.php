<?php

namespace classDbHandler\user;

use classDbHandler\DBHandlerParent;
use classModel\UserToken;

class UserTokenDBHandler extends DBHandlerParent
{
    public function crate(UserToken $user)
    {
        $this->createPDO('insert');
        $this->PDOLink->setCommand("INSERT INTO user_token (token, user_id, authorization_level, expiration_time) values (?,?,?,?)");
        $this->PDOLink->setValues([$user->getToken(), $user->getUserId(), $user->getAuthorizationLevel(), $user->getExpirationTime()]);
        $result = $this->PDOLink->execute();
        return $result;
    }

    public function select(string $token): ?UserToken
    {
        $userToken = new UserToken();
        $this->createPDO('select');
        $this->PDOLink->setCommand("select ut.token, ut.user_id, ut.authorization_level, ut.expiration_time from user_token as ut where ut.token = ?");
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

    public function checkTokenIsActive(UserToken $token): bool
    {
//
        return ($token->getExpirationTime() - (new \DateTime())->getTimestamp() > 0);
    }

    public function delete(UserToken $token)
    {
        $this->createPDO('delete');
        $this->PDOLink->setCommand("DELETE FROM user_token as ut where ut.user_id = ?");
        $this->PDOLink->setValues([$token->getUserId()]);
        $result = $this->PDOLink->execute();
        return $result;
    }
}
