<?php

namespace classDbHandler\user;

use classDbHandler\DBHandlerParent;
use classModel\User;
use exception\HttpResponseTriggerException;

/**
 * Class UserDBHandler database connector class for users
 * @package classDbHandler\user
 */
class UserDBHandler extends DBHandlerParent
{
    /**
     * checks if user with given name and email exists
     * @param User $user User data
     * @return array if user exists user id (in array) else empty array
     * @throws HttpResponseTriggerException wrong processor type, query error
     */
    public function checkUserNameOrEmailExists(User $user): array
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('select u.id from user as u where u.name = ? OR u.email = ?');
        $this->PDOLink->setValues([$user->getUserName(), $user->getEmail()]);
        return $this->PDOLink->execute();
    }

    /**
     * checks if user with given name and password exists
     * @param User $user User data
     * @return array if user exists user id (in array) else empty array
     * @throws HttpResponseTriggerException wrong processor type, query error
     */
    public function checkUserExistsWithNameAndPassword(User $user): array
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('select u.id from user as u where u.name = ? AND u.password = ?');
        $this->PDOLink->setValues([$user->getUserName(), $user->getPassword()]);
        return $this->PDOLink->execute();
    }

    /**
     * inserts a new user into the database
     * @param User $user user data
     * @return bool creation success
     * @throws HttpResponseTriggerException wrong processor type, query error
     */
    public function create(User $user): bool
    {
        $this->createPDO('insert');
        $this->PDOLink->setCommand('INSERT INTO user (id, name, password, email, authorization_level    ) values (?,?,?,?,?)');
        $this->PDOLink->setValues([null, $user->getUserName(), $user->getPassword(), $user->getEmail(), 1]);
        return $this->PDOLink->execute();
    }

    /**
     * returns a User Object by id if exists
     * @param int $userId user id
     * @return User|null User object if Exists , else null
     * @throws HttpResponseTriggerException  wrong processor type, query error
     */
    public function getAUserById(int $userId): ?User
    {
        $user = new User();
        $this->createPDO('select');
        $this->PDOLink->setCommand('select u.name, u.password, u.email, u.authorization_level from user as u where u.id = ?');
        $this->PDOLink->setValues([$userId]);
        $result = $this->PDOLink->execute();
        if (count($result) !== 1) {
            return null;
        }
        $result = $result[0];
        $user->setId($userId);
        $user->setUserName($result['name']);
        $user->setPassword($result['password']);
        $user->setEmail($result['email']);
        $user->setAuthorizationLevel($result['authorization_level']);
        return $user;
    }

}
