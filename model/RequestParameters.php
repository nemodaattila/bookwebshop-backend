<?php

namespace model;

/**
 * Class RequestParameters http request paraméterei mentődnek le ide
 * @package core\backend\model
 */
class RequestParameters
{
    /**
     * @var array http request url paraméterei
     */
    private array $urlParameters;
    /**
     * @var array http request bodyból származó paraméterek
     */
    private array $requestData;

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
     * paraméterek törlése
     */
    public function reset()
    {
        $this->urlParameters = [];
        $this->requestData = [];
    }

    /**
     * @return array összes paraméter array formában
     */
    public function getAlldata(): array
    {
        return get_object_vars($this);
    }
}
