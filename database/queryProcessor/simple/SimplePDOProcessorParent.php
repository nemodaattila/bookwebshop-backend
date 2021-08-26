<?php

namespace simpleDatabaseProcessor;

use exception\HttpResponseTriggerException;
use PDO;

/**
 * Class SimplePDOProcessorParent parent class for simplified PDO helper classes
 * @package simpleDatabaseProcessor
 */
abstract class SimplePDOProcessorParent
{
    /**
     * @var PDO | null PDO connection
     */
    protected ?PDO $pdo;
    /**
     * @var string sql query string
     */
    protected string $command;
    /**
     * @var array parameter of the query -> values to be bound
     */
    protected array $queryValues = [];

    public function __construct(?PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function setValues(mixed $values)
    {
        if (!is_array($values)) $values = [$values];
        $this->queryValues = $values;
    }

    public function nullPDO()
    {
        $this->pdo = null;
    }

    /**
     * @return mixed runs the query
     * @throws HttpResponseTriggerException query failed
     */
    public function execute(): mixed
    {
        $query = $this->pdo->prepare($this->getCommand());
        $success = $query->execute($this->getValues());
        if ($success === false) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'PDOSSSF', 'errorMessage' => $this->getCommand()], 500);
        }
        return true;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function setCommand(string $command)
    {
        $this->command = $command;
    }

    public function getValues(): array
    {
        return $this->queryValues;
    }

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function commit()
    {
        $this->pdo->commit();
    }

    public function rollback()
    {
        $this->pdo->rollBack();
    }

}
