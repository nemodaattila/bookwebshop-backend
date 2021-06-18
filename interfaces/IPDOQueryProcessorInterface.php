<?php

namespace interfaces;



use databaseSource\PDOQueryDataSource;

/**
 * Interface IPDOQueryProcessorInterface
 * @package core\backend\interfaces komplex PDO lekérdező query interface
 */
interface IPDOQueryProcessorInterface
{
    /**
     * @param PDOQueryDataSource $source query adatforrás hozzáadása
     */
    public function setSource(PDOQueryDataSource $source): void;
}
