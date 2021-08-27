<?php

namespace interfaces;

use databaseSource\PDOSelectQueryDataSource;

/**
 * interface for complex PDO query helpers
 * Interface IPDOQueryProcessorInterface
 * @package core\backend\interfaces
 */
interface IPDOQueryProcessorInterface
{
    /**
     * @param PDOSelectQueryDataSource $source setting a query data source
     */
    public function setSource(PDOSelectQueryDataSource $source): void;
}
