<?php

namespace databaseSource;

use interfaces\IPDOWhereConditionInterface;

/**
 * Class WhereConditionsBackboneClass több, egy PDO query-hez tartozó where feltétel tárolására, valamint a
 * query string összeállítására szolgáló osztály
 * al-where feltételként is példányosítható
 * @package core\backend\database\querySource
 */
class WhereConditionsBackboneClass extends WhereConditionParentClass implements IPDOWhereConditionInterface
{
    /**
     * új where feltétel hozzáadása (tömb tipusú feltételparaméter), adatok mentése
     * ,megfelelő osztályok létrehozása, az első feltétel $conditionOperator-át nem menti
     * @param string $operator a feltétel operátora pl: '='
     * @param array $parameters feltétel paraméterei pl: ['book_author.author_id','author.ID']
     * @param string|null $conditionOperator két feltétel közötti operátor pl: 'AND'
     * @param false $isBracketed ha true lekérdezésnél az egészet zárójelbe rakja
     * @throws RequestResultException ha nincs megadva $conditionOperátor (az első feltétel kivételével)
     * @example $x->addWhereCondition('=',['book_author.author_id','author.ID'],'AND');
     */
    public function addWhereCondition(string $operator, array $parameters, ?string $conditionOperator = null, bool $isBracketed = false)
    {
        $operator = strtoupper($operator);
        $class = match ($operator) {
            'BETWEEN' => new WhereBetweenConditionClass($parameters),
            default => new WhereConditionClassWithTwoParameter($operator, $parameters),
        };
        $this->conditions[] = [$class, $isBracketed];
        if (count($this->conditions) > 1) {
            $this->operators[] = $conditionOperator;
            if ($conditionOperator === null) {
                throw new RequestResultException(500, ['errorcode' => 'PDOACNCO']);
            }
        }
    }

    /**
     * új where feltétel (alfeltétel) hozzáadása (objektum tipusú feltételparaméter),az első feltétel $conditionOperator-át nem menti
     * @param WhereConditionsBackboneClass $class feltételobjektum
     * @param string|null $conditionOperator két feltétel közötti operátor pl: 'AND'
     * @param false $isBracketed ha true lekérdezésnél az egészet zárójelbe rakja
     * @todo tesztelni
     */
    public function addConditionObject(WhereConditionsBackboneClass $class, ?string $conditionOperator = null, bool $isBracketed = false)
    {
        $this->conditions[] = [$class, $isBracketed];
        if (count($this->conditions) > 1) {
            $this->operators[] = $conditionOperator;
        }
    }

    /**
     * @return array visszaadja az összes feltételt és operátort
     */
    public function getAll(): array
    {
        return [$this->conditions, $this->operators];
    }
}
