<?php

namespace classModel;

use interfaces\IConvertableToArrayInterface;

class User implements IConvertableToArrayInterface
{
    private int $id;

    private string $userName;
    private string $password;
    private string $email;
    private int $authorizationLevel = 1;

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getAuthorizationLevel(): int
    {
        return $this->authorizationLevel;
    }

    /**
     * @param int $authorizationLevel
     */
    public function setAuthorizationLevel(int $authorizationLevel): void
    {
        $this->authorizationLevel = $authorizationLevel;
    }

    /**
     * @param string $userName
     */
    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function getAllValueAsArray(): array
    {
        $values = [];
        foreach ($this as $key => $value) {
            $values[$key] = $value;
        }
        return $values;
    }
}
