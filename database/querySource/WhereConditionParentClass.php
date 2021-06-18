<?php

namespace databaseSource;

use core\backend\model\RequestResultException;
use interfaces\IPDOWhereConditionInterface;

/**
 * Class WhereConditionParentClass a PDO where feltételeket megvalósító osztályok ősosztálya
 * @package core\backend\database\querySource
 */
abstract class WhereConditionParentClass implements IPDOWhereConditionInterface
{

    /**
     * @var array where feltételek tömbje | (IPDOWhereConditionInterface)[]
     */
    protected array $conditions = [];

    /**
     * @var array feltételek közti operátorok tömbje pl: [AND,AND,OR]
     */
    protected array $operators = [];

    /**
     * visszadja a vizsgált elemet stringként, mely a query string része lesz
     * ha a $condotion string egyszerűen visszadja, ha viszont az IPDOWhereConditionInterface-t megvalósító class
     * akkor annak kéri le a query-stringjét, és adja vissza
     * @param string|IPDOWhereConditionInterface $condition a feltételrészt taralmazó szrting vagy objektum
     * @return string a query string szakasz
     * @throws RequestResultException ha a $condition nem string vagy IPDOWhereConditionInterface class
     */
    protected function getQueryStringPart(string|IPDOWhereConditionInterface $condition): string
    {
        if (gettype($condition) === 'string') {
            return $condition;
        }
        if (in_array('core\backend\interfaces\IPDOWhereConditionInterface', class_implements($condition))) {
            return $condition->getQueryString();
        }
        throw new RequestResultException(500, ['errorCode' => 'PDOWCPCBCT']);
    }

    /**
     * @return string query WHERE string szakasz összeállítása
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
