<?php

namespace complexDatabaseProcessor;

use databaseSource\PDOSelectQueryDataSource;
use databaseSource\PDOUpdateQueryDataSource;
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
     * @var PDOSelectQueryDataSource datasource for PDO
     */
    protected PDOSelectQueryDataSource|PDOUpdateQueryDataSource $source;

    /**
     * @var PDO PDO connection
     */
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function setSource(PDOSelectQueryDataSource|PDOUpdateQueryDataSource $source): void
    {
        $this->source = $source;
    }

    /**
     * if a sub-query exists, compiles and returns it
     * @return string al-query string
     */
    protected function getSubQueryAsAttribute(): string
    {
        $query = '';
        $subQuery = $this->source->getSubQueryAsAttribute();
        if (count($subQuery) !== 0) {
            $query = ',';
            foreach ($subQuery as [$processor, $source, $alias]) {
                $processor->setSource($source);
                $query .= '(' . $processor->createQuery() . ') AS ' . $alias . ' ';
            }
        }
        return $query;
    }

    protected function runQuery(string $queryString): bool
    {
        $query = $this->pdo->prepare($queryString);
        $values = $this->source->getBoundValues();
        if (!empty($values)) {
            foreach ($values as $key => $value) {
                $id = ($value[2] !== null) ? $value[2] : $key + 1;
                $query->bindValue($id, $value[0], $value[1]);
            }
        }
        return $query->execute();
    }

}
