<?php

namespace exception;

use Exception;
use Throwable;

/**
 * Class HttpResponseTriggerException it's a fake exception for triggering http response
 * @package exception
 */
class HttpResponseTriggerException extends Exception
{
    /**
     * @var int http code of the response
     */
    private int $httpCode;

    /**
     * @var bool state of correctness of the response data
     * e.g.: false for validation error
     */
    private bool $success;

    /**
     * @var mixed data for response
     */
    private mixed $data;

    public function __construct(bool $success, mixed $data, int $httpCode = 200, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->success = $success;
        $this->data = $data;
        $this->httpCode = $httpCode;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
