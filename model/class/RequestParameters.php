<?php

namespace classModel;

/**
 * Class RequestParameters stores data originating from http requests
 * @package core\backend\model
 */
class RequestParameters
{
    /**
     * @var array request parameters from url
     */
    private array $urlParameters;
    /**
     * @var array parameters from request body
     */
    private array $requestData;

    /**
     * adds an url parameter
     * @param string $urlParameter parameter
     */
    public function addUrlParameter(string $urlParameter): void
    {
        $this->urlParameters[] = $urlParameter;
    }

    public function setRequestData(array $requestData): void
    {
        $this->requestData = $requestData;
    }

    public function getUrlParameters(): array
    {
        return $this->urlParameters;
    }

    public function getRequestData(): array
    {
        return $this->requestData;
    }

    /**
     * resetting parameters to empty
     */
    public function reset()
    {
        $this->urlParameters = [];
        $this->requestData = [];
    }

    /**
     * @return array return all parameters as array
     */
    public function getAllData(): array
    {
        return get_object_vars($this);
    }


}
