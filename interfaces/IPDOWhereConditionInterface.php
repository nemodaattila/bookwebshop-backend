<?php

namespace interfaces;

/**
 * interface for creating PDO WHERE conditions
 * Interface IPDOWhereConditionInterface
 * @package core\backend\interfaces
 */
interface IPDOWhereConditionInterface
{
    /**
     * @return string returns the query's WHERE part as string
     */
    public function getQueryString(): string;
}
