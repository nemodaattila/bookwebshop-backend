<?php

namespace databaseSource;

use exception\HttpResponseTriggerException;
use interfaces\IPDOWhereConditionInterface;

/**
 * Class WhereConditionClassWithTwoParameter class for where conditions with 2 parameters, and which
 * format is: <parameter1> <operator> <parameter2> e.g.: id = 11
 * @package databaseSource
 */
class WhereConditionClassWithTwoParameter extends WhereConditionParentClass implements IPDOWhereConditionInterface
{
    /**
     * @var string operator of the condition e.g.: '=', 'LIKE'
     */
    private string $operator;

    /**
     * @var array the two parameters of the condition , can be scalar or can implement IPDOWhereConditionInterface
     */
    private array $parameters;

    /**
     *  checks as saves the condition parameters and operator
     * @param string $operator condition operator
     * @param array $parameters condition parameters (2)
     * @throws HttpResponseTriggerException if the count of parameters are not 2
     * @example ('=', ['person.id', 'customer_id']), ('LIKE', ['book.isbn', %963%])
     */
    public function __construct(string $operator, array $parameters)
    {
        if (count($parameters) !== 2) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'PDOWC2PC'], 500);
        }
        $this->operator = $operator;
        $this->parameters = $parameters;
    }

    /**
     * @return string returns the parameter as string
     * @throws HttpResponseTriggerException wrong condition type
     */
    public function getQueryString(): string
    {
        return $this->getQueryStringPart($this->getQueryStringPart($this->parameters[0]) . ' ' . $this->operator . ' ' . $this->getQueryStringPart($this->parameters[1]));
    }

}
