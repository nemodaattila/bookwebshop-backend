<?php

namespace interfaces;

/**
 * Interface IPDOWhereConditionInterface
 * @package core\backend\interfaces PDO query WHERE feltételének megadására és leképezésére képes osztályok interfece
 */
interface IPDOWhereConditionInterface
{
    /**
     * @return string visszaadja a WHERE query stringszakaszt
     */
    public function getQueryString(): string;
}
