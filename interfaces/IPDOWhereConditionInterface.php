<?php

namespace interfaces;

/**
 * Interface IPDOWhereConditionInterface
 * @package core\backend\interfaces interface for creating PDO WHERE conditions
 */
interface IPDOWhereConditionInterface
{
    /**
     * @return string returns the query's WHERE part as string
     */
    public function getQueryString(): string;
}
