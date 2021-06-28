<?php

namespace simpleDatabaseProcessor;

use exception\HttpResponseTriggerException;
use PDO;

/**
 * Class SimpleSelectPDOProcessor helper class for handling simplified select PDO queries
 * @package simpleDatabaseProcessor
 */
class SimpleSelectPDOProcessor extends SimplePDOProcessorParent
{
    /**
     * @var string type of fetching | fetch or fetchAll
     */
    protected string $fetchType = 'fetchAll';

    /**
     * @var int query method e.g: PDO::FETCH_ASSOC or 1
     */
    protected int $fetchMode = PDO::FETCH_ASSOC;

    public function getFetchType(): string
    {
        return $this->fetchType;
    }

    public function getFetchMode(): int
    {
        return $this->fetchMode;
    }

    /**
     * sets the type of fetching
     * @param string $fetchType fetch or fetchAll
     * @throws HttpResponseTriggerException if fetchType is not fetch or fetchAll
     */
    public function setFetchType(string $fetchType): void
    {
        if (!in_array($fetchType, ['fetch', 'fetchAll']))
            throw new HttpResponseTriggerException(false, ['errorCode' => 'PDOFTBT'], 500);
        $this->fetchType = $fetchType;
    }

    public function setFetchMode(int $fetchMode): void
    {
        $this->fetchMode = $fetchMode;
    }

    /**
     * runs the query and returns the result
     * @return mixed result of query
     * @throws HttpResponseTriggerException on query error
     */
    public function execute(): mixed
    {
        $query = $this->pdo->prepare($this->getCommand());
        $success = $query->execute($this->getValues());
        if ($success === false) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'PDOSSSF', 'errorMessage' => $this->getCommand()], 500);
        }
        $rt = $this->getFetchType();
        return $query->$rt($this->getFetchMode());
    }
}
