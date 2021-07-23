<?php

namespace classModel;

class UserToken
{
    private string $token;
    private int $userId;
    private int $authorizationLevel;
    private int $expirationTime;

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
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
     * @return int
     */
    public function getExpirationTime(): int
    {
        return $this->expirationTime;
    }

    /**
     * @param int $expirationTime
     */
    public function setExpirationTime(int $expirationTime): void
    {
        $this->expirationTime = $expirationTime;
    }

}
