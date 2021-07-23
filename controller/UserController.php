<?php

namespace controller;

use classDbHandler\user\UserDBHandler;
use classModel\User;
use exception\HttpResponseTriggerException;

class UserController
{
    private UserDBHandler $DBHandler;

    public function __construct()
    {
        $this->DBHandler = new UserDBHandler();
    }

    public function registerUser(array $regData)
    {
        $user = new User();
        $user->setEmail($regData['email']);
        $user->setUserName($regData['userName']);
        $user->setPassword($this->hashPassword($regData['password']));
        $userExists = $this->DBHandler->checkUserNameOrEmailExists($user);
        if (!empty($userExists)) {
            return [false, ['errorCode' => 'UE']];
        }
        $result = $this->DBHandler->crate($user);
        if ($result) {
            return [true, $result];
        } else
            return [false, ['errorCode' => 'UCE']];
    }

    private function hashPassword(string $password): string
    {
        return bin2hex(mhash(MHASH_SHA384, $password));
    }

    private function comparePasswords(User $loginUser, User $userDB): bool
    {
        return $loginUser->getPassword() === $userDB->getPassword();
    }

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

    public function getAUserById(int $userId): User
    {
        $userObj = $this->DBHandler->getAUserById($userId);
        if (!$userObj) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'UNE']);
        }
        return $userObj;
    }
}
