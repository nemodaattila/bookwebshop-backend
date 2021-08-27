<?php

namespace databaseSource;

use exception\HttpResponseTriggerException;

/**
 * Class PDOQueryDataSource datasource for complex PDO queries
 * @package databaseSource
 * @see PDOQueryProcessorParent
 */
class PDOSelectQueryDataSource extends PDOQueryWithWhereDataSource
{

    /**
     * @var bool limit is enabled
     */
    private bool $hasLimit = false;
    /**
     * @var bool offset is enabled
     */
    private bool $hasOffset = false;

    /**
     * @var string ORDER BY attribute
     * TODO több paraméter megadása
     */
    private string $order;
    /**
     * @var string direction of the order ASC/DESC
     */
    private string $orderDirection;
    /**
     * @var bool enable SELECT DISTINCT
     */
    private bool $distinct = false;

    //getters/setters

    public function isDistinct(): bool
    {
        return $this->distinct;
    }

    public function setDistinct()
    {
        $this->distinct = true;
    }

    public function hasLimit(): bool
    {
        return $this->hasLimit;
    }

    public function hasOffset(): bool
    {
        return $this->hasOffset;
    }

    public function getOrder(): ?string
    {
        if (isset($this->order))
            return $this->order;
        return null;
    }

    /**
     * sets the order parameter
     * @throws HttpResponseTriggerException if table of the order parameter not exists
     */
    public function setOrder(string $order): void
    {
        $order = $this->checkTableExists($order);
        $this->order = $order;
    }

    public function getOrderDirection(): ?string
    {
        if (isset($this->orderDirection))
            return $this->orderDirection;
        return null;
    }

    public function setOrderDirection(string $orderDirection): void
    {
        $this->orderDirection = $orderDirection;
    }

    /**
     * enable limit
     */
    public function enableLimit()
    {
        $this->hasLimit = true;
    }

    /**
     * enable offset
     */
    public function enableOffset()
    {
        $this->hasOffset = true;
    }

    /**
     * returns offset and limit as count (bool -> int) (true, true) -> 2
     * @return int count
     */
    public function countOfActiveLimitAndOffset(): int
    {
        return (int)$this->hasOffset + (int)$this->hasLimit;
    }

}
