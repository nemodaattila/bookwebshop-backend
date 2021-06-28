<?php

namespace databaseSource;

use exception\HttpResponseTriggerException;
use interfaces\IPDOWhereConditionInterface;

/**
 * Class WhereConditionsBackboneClass class for storing and processing multiple where conditions,
 * which belongs to the same PDO query, can be a sub-query
 * @package databaseSource
 */
class WhereConditionsBackboneClass extends WhereConditionParentClass implements IPDOWhereConditionInterface
{
    /**
     * adds new where condition (array), creates where class, saves data
     * @param string $operator condition operator e.g: '='
     * @param array $parameters condition parameters e.g: ['book_author.author_id','author.ID']
     * @param string|null $conditionOperator operator between two conditions pl: 'AND'
     * @param false $isBracketed if true the conditions will be bracketed
     * @throws HttpResponseTriggerException missing operator, not right parameter number
     * @example $x->addWhereCondition('=',['book_author.author_id','author.ID'],'AND');
     */
    public function addWhereCondition(string $operator, array $parameters, ?string $conditionOperator = null, bool $isBracketed = false)
    {
        $operator = strtoupper($operator);
        $class = match ($operator) {
            'BETWEEN' => new WhereBetweenConditionClass($parameters),
            default => new WhereConditionClassWithTwoParameter($operator, $parameters),
        };
        $this->conditions[] = [$class, $isBracketed];
        if (count($this->conditions) > 1) {
            $this->operators[] = $conditionOperator;
            if ($conditionOperator === null) {
                throw new HttpResponseTriggerException(false, ['errorCode' => 'PDOACNCO'], 500);
            }
        }
    }

    /**
     * add new condition (sub-condition as object)
     * @param WhereConditionsBackboneClass $class condition class
     * @param string|null $conditionOperator operator between two component pl: 'AND'
     * @param false $isBracketed if true the condition will be bracketed
     * @todo tesztelni
     */
    public function addConditionObject(WhereConditionsBackboneClass $class, ?string $conditionOperator = null, bool $isBracketed = false)
    {
        $this->conditions[] = [$class, $isBracketed];
        if (count($this->conditions) > 1) {
            $this->operators[] = $conditionOperator;
        }
    }

    /**
     * returns all conditions and operators
     * @return array [conditions, operators]
     */
    public function getAll(): array
    {
        return [$this->conditions, $this->operators];
    }
}
