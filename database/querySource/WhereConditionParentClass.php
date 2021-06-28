<?php

namespace databaseSource;

use exception\HttpResponseTriggerException;
use interfaces\IPDOWhereConditionInterface;

/**
 * Class WhereConditionParentClass parent for where condition classes
 * @package databaseSource
 */
abstract class WhereConditionParentClass implements IPDOWhereConditionInterface
{

    /**
     * @var array array of where conditions| (IPDOWhereConditionInterface)[]
     */
    protected array $conditions = [];

    /**
     * @var array array for conditional operator pl: [AND,AND,OR]
     */
    protected array $operators = [];

    /**
     * checks the type of condition,
     * if string return it, if implements IPDOWhereConditionInterface ,
     * creates string from it
     * @param string|IPDOWhereConditionInterface $condition condition
     * @return string query as string
     * @throws HttpResponseTriggerException if condition is not string or IPDOWhereConditionInterface
     */
    protected function getQueryStringPart(string|IPDOWhereConditionInterface $condition): string
    {
        if (gettype($condition) === 'string') {
            return $condition;
        }
        if (in_array('core\backend\interfaces\IPDOWhereConditionInterface', class_implements($condition))) {
            return $condition->getQueryString();
        }
        throw new HttpResponseTriggerException(500, ['errorCode' => 'PDOWCPCBCT']);
    }

    /**
     * @return string creates query string from the where condition class
     */
    public function getQueryString(): string
    {
        $condCount = count($this->conditions);
        if ($condCount === 0) {
            return '';
        }
        $query = '';
        for ($i = 0; $i < $condCount; $i++) {
            if ($i > 0) {
                $query .= $this->operators[$i - 1] . ' ';
            }
            [$condClass, $bracketed] = $this->conditions[$i];
            $string = $condClass->getQueryString();
            if ($bracketed === true)
                $string = "(" . $string . ')';
            $query .= $string . ' ';
        }
        return $query;
    }
}
