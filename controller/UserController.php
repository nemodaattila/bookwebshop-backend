<?php

namespace controller;

use classDbHandler\user\UserDBHandler;
use classModel\User;
use exception\HttpResponseTriggerException;

/**
 * Class UserController controller for handling users
 * @package controller
 */
class UserController
{
    private UserDBHandler $DBHandler;

    public function __construct()
    {
        $this->DBHandler = new UserDBHandler();
    }

    /**
     * creates a new USer and saves to database, name and email must be unique
     * @param array $regData registration data
     * @return array [<bool> action success, <mixed> result or error data (email or name exists, database error) ]
     * @throws HttpResponseTriggerException database errors
     */
    public function registerUser(array $regData): array
    {
        $user = new User();
        $user->setEmail($regData['email']);
        $user->setUserName($regData['userName']);
        $user->setPassword($this->hashPassword($regData['password']));
        $userExists = $this->DBHandler->checkUserNameOrEmailExists($user);
        if (!empty($userExists)) {
            return [false, ['errorCode' => 'UE']];
        }
        $result = $this->DBHandler->create($user);
        if ($result) {
            return [true, true];
        } else
            return [false, ['errorCode' => 'UCE']];
    }

    /**
     * hashes the given plain password
     * @param string $password password
     * @return string hashed password
     * TODO create more complex hash
     */
    private function hashPassword(string $password): string
    {
        return bin2hex(mhash(MHASH_SHA384, $password));
    }

    /**
     * compares two passwords
     * @param User $loginUser password from login data
     * @param User $userDB password from database
     * @return bool result of comparison
     */
    private function comparePasswords(User $loginUser, User $userDB): bool
    {
        return $loginUser->getPassword() === $userDB->getPassword();
    }

    /**
     * checks if a user exists, by login data, checks password, if true returns user, else error message
     * @param array $loginData login data
     * @return array [<bool> action success, <mixed> user or error data (user not exists with data, bad password), database error]
     * @throws HttpResponseTriggerException user not exists , database errors
     * TODO password check really needed ???
     */
    public function loginUser(array $loginData): array
    {
        $tempUser = new User();
        $tempUser->setUserName($loginData['name']);
        $tempUser->setPassword($this->hashPassword($loginData['password']));
        $userExists = $this->DBHandler->checkUserExistsWithNameAndPassword($tempUser);
        if (count($userExists) !== 1) {
            return [false, ['errorCode' => 'ULEPN']];
        }
        $id = $userExists[0]['id'];
        $user = $this->getAUserById($id);
        if (!$this->comparePasswords($tempUser, $user))
            return [false, ['errorCode' => 'ULEIP']];
        return [true, $user];
    }

    /**
     * return a user based on ID
     * @param int $userId user id
     * @return User user Object
     * @throws HttpResponseTriggerException user id not exists, db error
     */
    public function getAUserById(int $userId): User
    {
        $userObj = $this->DBHandler->getAUserById($userId);
        if (!$userObj) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'UNE']);
        }
        return $userObj;
    }
}
