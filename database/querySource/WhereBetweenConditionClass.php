<?php

namespace databaseSource;

use exception\HttpResponseTriggerException;
use interfaces\IPDOWhereConditionInterface;

/**
 * Class WhereBetweenConditionClass adding PDO BETWEEN WHERE SQL parameters
 * @package core\backend\database\querySource
 */
class WhereBetweenConditionClass extends WhereConditionParentClass implements IPDOWhereConditionInterface
{

    /**
     * @var array parameters of the between condition
     */
    private array $parameters;

    /**
     * saving parameters, parameter count check
     * @param array $parameters parameters, format: [<value to be checked>,<minimum value>, <maximum value>]
     * @throws HttpResponseTriggerException if parameter count is not 3
     */
    public function __construct(array $parameters)
    {
        if (count($parameters) !== 3) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'PDOWBCPC'], 500);
        }
        $this->parameters = $parameters;
    }

    /**
     * return the parameters as where query string part
     * @return string string part
     * @throws HttpResponseTriggerException wrong condition type
     */
    public function getQueryString(): string
    {
        $param1 = $this->getQueryStringPart($this->parameters[0]);
        $param2 = $this->getQueryStringPart($this->parameters[1]);
        $param3 = $this->getQueryStringPart($this->parameters[2]);
        return $param1 . " BETWEEN " . $param2 . ' AND ' . $param3;
    }

}
