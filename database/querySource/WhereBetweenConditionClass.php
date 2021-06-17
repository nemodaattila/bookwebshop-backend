<?php

namespace core\backend\database\querySource;

use core\backend\interfaces\IPDOWhereConditionInterface;
use core\backend\model\RequestResultException;

/**
 * Class WhereBetweenConditionClass PDO BETWEEN SQL WHERE paraméterek megadására
 * @package core\backend\database\querySource
 */
class WhereBetweenConditionClass extends WhereConditionParentClass implements IPDOWhereConditionInterface
{

    /**
     * @var array BETWEEN feltétel paraméterei;
     */
    private array $parameters;

    /**
     * WhereConditionClassWithTwoParameter constructor. BETWEEN paraméterek mentése, paraméter darabszám ellenőrzés
     * @param array $parameters BETWEEN feltétel paraméterei, formátum: [<összehasonlítandó érték>,<minimum érték>, <maximum érték>]
     * @throws RequestResultException ha paraméterek száma nem 3
     */
    public function __construct(array $parameters)
    {
        if (count($parameters) !== 3) {
            throw new RequestResultException('500', ['errorCode' => 'PDOWBCPC']);
        }
        $this->parameters = $parameters;
    }

    /**
     * visszaadja a BETWEEN feltételt stringként
     * @return string BETWEEN feltétel string-szakasz
     * @throws RequestResultException ha valamelyik paraméter tipus nem megfelelő
     */
    public function getQueryString(): string
    {
        $param1 = $this->getQueryStringPart($this->parameters[0]);
        $param2 = $this->getQueryStringPart($this->parameters[1]);
        $param3 = $this->getQueryStringPart($this->parameters[2]);
        return $param1 . " BETWEEN " . $param2 . ' AND ' . $param3;
    }

}
