<?php

namespace complexDatabaseProcessor;

use databaseSource\PDOQueryDataSource;
use interfaces\IPDOQueryProcessorInterface;
use PDO;

/**
 * Class ComplexPDOQueryProcessorParent parent for complex PDO handler classes
 * @package complexDatabaseProcessor
 * @todo named placeholder megvalósítása
 */
abstract class PDOQueryProcessorParent implements IPDOQueryProcessorInterface
{
    /**
     * @var PDOQueryDataSource datasource for PDO
     */
    protected PDOQueryDataSource $source;

    /**
     * @var PDO PDO connection
     */
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function setSource(PDOQueryDataSource $source): void
    {
        $this->source = $source;
    }
}
