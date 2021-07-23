<?php

namespace classDbHandler\user;

use classDbHandler\DBHandlerParent;
use classModel\User;

class UserDBHandler extends DBHandlerParent
{
    public function checkUserNameOrEmailExists(User $user)
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand("select u.id from user as u where u.name = ? OR u.email = ?");
        $this->PDOLink->setValues([$user->getUserName(), $user->getEmail()]);
        $result = $this->PDOLink->execute();
        return $result;
    }

    public function checkUserExistsWithNameAndPassword(User $user)
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand("select u.id from user as u where u.name = ? OR u.password = ?");
        $this->PDOLink->setValues([$user->getUserName(), $user->getPassword()]);
        $result = $this->PDOLink->execute();
        return $result;
    }

    public function crate(User $user)
    {
        $this->createPDO('insert');
        $this->PDOLink->setCommand("INSERT INTO user (id, name, password, email, authorization_level    ) values (?,?,?,?,?)");
        $this->PDOLink->setValues([null, $user->getUserName(), $user->getPassword(), $user->getEmail(), 1]);
        $result = $this->PDOLink->execute();
        return $result;
    }

    public function getAUserById(int $userId): ?User
    {
        $user = new User();
        $this->createPDO('select');
        $this->PDOLink->setCommand("select u.name, u.password, u.email, u.authorization_level from user as u where u.id = ?");
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
