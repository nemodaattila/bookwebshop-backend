<?php

namespace interfaces;

use databaseSource\PDOQueryDataSource;

/**
 * Interface IPDOQueryProcessorInterface
 * @package core\backend\interfaces interface for complex PDO query helpers
 */
interface IPDOQueryProcessorInterface
{
    /**
     * @param PDOQueryDataSource $source setting a query data source
     */
    public function setSource(PDOQueryDataSource $source): void;
}
