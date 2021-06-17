<?php

namespace core\backend\database\queryProcessor\complex;
use core\backend\database\querySource\PDOQueryDataSource;
use core\backend\interfaces\IPDOQueryProcessorInterface;
use PDO;

/**
 * Class PDOQueryProcessorParent komplex PDO kezelő osztályok őse
 * @package core\backend\database\queryProcessor\complex
 * @todo named placeholder megvalósítása
 */
abstract class PDOQueryProcessorParent implements IPDOQueryProcessorInterface
{
    /**
     * @var PDOQueryDataSource adatforrás a PDO-hoz
     */
    protected PDOQueryDataSource $source;

    /**
     * @var PDO pdo csatlakozás
     */
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo=$pdo;
    }
    public function setSource(PDOQueryDataSource $source): void
    {
        $this->source = $source;
    }
}
