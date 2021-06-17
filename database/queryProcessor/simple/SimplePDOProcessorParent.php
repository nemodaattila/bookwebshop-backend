<?php

namespace core\backend\database\queryProcessor\simple;

use core\backend\model\RequestResultException;
use PDO;

/**
 * Class SimplePDOProcessorParent egyszerűsített PDO segédosztályok ősosztálya
 * @package core\backend\database\QueryProcessor\simple
 */
abstract class SimplePDOProcessorParent
{
    /**
     * @var PDO PDO csatlakozás
     */
    protected PDO $pdo;
    /**
     * @var string a sql lekérdezés stringje
     */
    protected string $command;
    /**
     * @var array a lekérdezés paraméterei - bind-olandó értékek
     */
    protected array $queryValues = [];

    /**
     * a lekérdezés megadása egyszerű string formában
     * @param string az sql lekérdezés
     */
    public function setCommand(string $command)
    {
        $this->command = $command;
    }

    /**
     * visszadja a beállított query stringet
     * @return string  a query string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @param mixed $values a lekérdezés paramétereinek megadása
     */
    public function setvalues(mixed $values)
    {
        if (!is_array($values)) $values = [$values];
        $this->queryValues = $values;
    }

    /**
     * visszaadja a beállított értékeket a lekérdezéshez
     * @return array az értékek tömbje
     */
    public function getValues(): array
    {
        return $this->queryValues;
    }

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return mixed a lekérdezés végrehajtása
     * @throws RequestResultException ha nem sikerült a lekérdezés, kivétel tartalma: (httpkód 500, [hibakód, querystring])
     */
    public function execute(): mixed
    {
        $query = $this->pdo->prepare($this->getCommand());
        $success = $query->execute($this->getValues());
        if ($success === false) {
            throw new RequestResultException(500, ['errorCode' => 'PDOSSSF', 'errorMessage' => $this->getCommand()]);
        }
        return true;
    }
}
