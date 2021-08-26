<?php

namespace interfaces;

use databaseSource\PDOQueryDataSource;

/**
 * interface for complex PDO query helpers
 * Interface IPDOQueryProcessorInterface
 * @package core\backend\interfaces
 */
interface IPDOQueryProcessorInterface
{
    /**
     * @param PDOQueryDataSource $source setting a query data source
     */
    public function setSource(PDOQueryDataSource $source): void;
}
