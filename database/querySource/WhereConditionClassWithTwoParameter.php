<?php

namespace databaseSource;

use interfaces\IPDOWhereConditionInterface;

/**
 * Class WhereConditionClassWithTwoParameter azon PDO WHERE feltételek osztálya, melyek 2 paraméterből állnak és
 * erre a formára épülnek: <paraméter1> <operátor> <parameter2> pl: id = 11
 * @package core\backend\database\querySource
 */
class WhereConditionClassWithTwoParameter extends WhereConditionParentClass implements IPDOWhereConditionInterface
{
    /**
     * @var string a feltétel operátora pl: '=', 'LIKE'
     */
    private string $operator;

    /**
     * @var array a feltétel két paramétere, lehetnek skaláris változók, vagy olyan osztályok, melyek a IPDOWhereConditionInterface-t
     * implementálják , vagyis bármilyen WHERE feltételt valósítanak meg
     */
    private array $parameters;

    /**
     * WhereConditionClassWithTwoParameter constructor. ellenőrzi és menti az operátort és a paramétereket
     * @param string $operator feltétel operátora
     * @param array $parameters a feltétel paraméterei (2 db)
     * @throws RequestResultException ha a feltételek száma nem 2
     * @example ('=', ['person.id', 'customer_id']), ('LIKE', ['book.isbn', %963%])
     */
    public function __construct(string $operator, array $parameters)
    {
        if (count($parameters) !== 2) {
            throw new RequestResultException('500', ['errorCode' => 'PDOWC2PC']);
        }
        $this->operator = $operator;
        $this->parameters = $parameters;
    }

    /**
     * @return string visszaadja a feltételt stringként
     * @throws RequestResultException ha valamely paraméter tipusa nem megfelelő
     */
    public function getQueryString(): string
    {
        return $this->getQueryStringPart($this->parameters[0]) . ' ' . $this->operator . ' ' . $this->getQueryStringPart($this->parameters[1]);
    }

}
