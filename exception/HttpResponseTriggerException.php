<?php

namespace exception;

use Throwable;

class HttpResponseTriggerException extends \Exception
{
    private int $httpCode=200;
    private bool $success;
    private mixed $data;

    public function __construct(bool $success, mixed $data, int $httpCode=200, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->success = $success;
        $this->data = $data;
        $this->httpCode = $httpCode;
    }



    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }


}
