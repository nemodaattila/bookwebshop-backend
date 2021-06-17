<?php

namespace core\backend\database\queryProcessor\simple;

use core\backend\model\RequestResultException;
use PDO;

/**
 * Class SimpleSelectPDOProcessor segédosztály select PDO query létrehozására és futtatására
 * @package core\backend\database\queryProcessor\simple
 */
class SimpleSelectPDOProcessor extends SimplePDOProcessorParent
{
    /**
     * @var string hogyan fetcheljük a query eredményét - egyelőre fetch és fetchAll
     */
    protected string $fetchType = 'fetchAll';

    /**
     * @var int a query fetchelés módja pl: PDO::FETCH_ASSOC vagy 1
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

    /** beállitja hogyan adja vissza az eredményt a query
     * @param string $fetchType feth vagy fetchAll
     * @throws RequestResultException ha a $fetchType nem nem fetch vagy fetchAll
     */
    public function setFetchType(string $fetchType): void
    {
        if (!in_array($fetchType, ['fetch', 'fetchAll']))
            throw new RequestResultException(400, ['errorCode' => 'PDOFTBT']);
        $this->fetchType = $fetchType;
    }

    /**
     * beállítja a fetchelés módját
     * @param int $fetchMode
     */
    public function setFetchMode(int $fetchMode): void
    {
        $this->fetchMode = $fetchMode;
    }

    /**
     * lefuttataja a lekérdezést és visszaadja az eredményt
     * @return mixed a select lekérezés eredménye
     * @throws RequestResultException ha a lekérdezés meghiúsult , kivétel tartalma: (httpkód 500, [hibakód, querystring])
     */
    public function execute(): mixed
    {
        $query = $this->pdo->prepare($this->getCommand());
        $success = $query->execute($this->getValues());
        if ($success === false) {
            throw new RequestResultException(500, ['errorcode' => 'PDOSSSF', 'errorMessage' => $this->getCommand()]);
        }
        $rt = $this->getFetchType();
        return $query->$rt($this->getFetchMode());
    }
}
