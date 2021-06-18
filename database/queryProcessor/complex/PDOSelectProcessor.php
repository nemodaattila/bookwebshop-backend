<?php

namespace complexDatabaseProcessor;


use databaseSource\PDOQueryDataSource;
use PDO;

/**
 *
 * Class ComplexPDOSelectProcessor PDO select utasítás létrehozására és feldolgozására szolgáló osztály
 * @package core\backend\database\queryProcessor\complex
 */
class PDOSelectProcessor extends PDOQueryProcessorParent
{
    /**
     * @var string a query eredmény fetchelési módja |
     * fetch vagy fetchAll
     */
    protected string $fetchType = 'fetchAll';
    /**
     * @var int eredmény fetchelési módja |
     * pl: PDO::FETCH_ASSOC, PDO::FETCH_ARRAY
     */
    protected int $fetchMode = PDO::FETCH_ASSOC;

    /**
     * a PDO lekérdezés paramétereinek mentése, majd feldolgozása
     * @param PDOQueryDataSource $source - query adatforrás
     * @param string $fetchType - fetch típus
     * @param int $fetchMode - fetch mód
     * @return array a lekérdezés eredménye
     * @throws RequestResultException ha a fetchType nem fetch vagy fetchAll
     * @example selectProc->query($source, 'fetch', PDO::FETCH_LAZY)
     */
    public function query(PDOQueryDataSource $source, string $fetchType = 'fetchAll', int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $this->setSource($source);
        $this->fetchMode = $fetchMode;
        if (!in_array($fetchType, ['fetch', 'fetchAll']))
            throw new RequestResultException(400, ['errorCode' => 'PDOFTBT']);
        $this->fetchType = $fetchType;
        return $this->runQuery($this->createQuery());
    }

    /**
     * összeállítja és visszaadja egy lekérdezés query stringjét adatObjektumból
     * @return string querystring
     */
    public function createQuery(): string
    {
        $query = "SELECT " . $this->getTableAndAttributesQuery();
        $where = $this->source->getQueryWhere();
        if ($where !== '')
            $query .= ' WHERE ' . $where;
//        $query .= $this->getGroupGuery();
        $query .= $this->getOrderByQuery();
        $query .= $this->getLimitAndOffsetQuery();
        //        var_dump($query);
        return trim(str_replace('  ', ' ', $query));
    }

    /**
     * @return string összeállítja és visszaadja egy darabszámlekérdező query stringjét adatObjektumból
     */
    private function createCountQuery(): string
    {
        $query = "SELECT COUNT(*) AS COUNT FROM ( SELECT " . $this->getTableAndAttributesQuery();
        $where = $this->source->getQueryWhere();
        if ($where !== '')
            $query .= ' WHERE ' . $where;
        $query .= ') AS COUNTSUB';
        return trim(str_replace('  ', ' ', $query));
    }

    /**
     * lefuttat egy összdarabszám lekérdező select query-t
     * @param PDOQueryDataSource|null $source query adatForrás
     * @return int a darabszám - a lekérdezés eredménye
     * @throws RequestResultException ha a source null | !!! korábbi source újrafelhasználható, akkor nem kell megadni
     */
    public function countQuery(?PDOQueryDataSource $source = null): int
    {
        if ($source !== null)
            $this->setSource($source);
        if ($this->source !== null) {
            return $this->runCountQuery($this->createCountQuery());
        }
        throw new RequestResultException(500, ['errorCode' => 'PDOSPCQSE']);
    }

    /**
     * visszaadja az adatosztály alapján a select querystring azon szakaszát, mely tartalmazza
     * a résztvevő táblákat és attribumokat | pl: SELECT p.name FROM person as P
     * @return string a query string szakasz
     */
    private function getTableAndAttributesQuery(): string
    {
        $query = '';
        if ($this->source->isDistinct()) {
            $query = 'DISTINCT ';
        }
        $tables = [];
        $attribs = [];
        $params = $this->source->getTablesAndAttributes();
        foreach ($params as $table => ['alias' => $alias, 'attributes' => $attributes]) {
            foreach ($attributes as ['name' => $attributeName, 'alias' => $attributeAlias]) {
                $attribs[] = ($alias !== null ? $alias : $table) . '.' . $attributeName . ($attributeAlias !== null ? ' AS ' . $attributeAlias : '');
            }
            $tables[] = $table . ($alias !== null ? ' AS ' . $alias : '');
        }
        $query .= implode(', ', $attribs) . ' ' . $this->getSubQueryAsAttribute() . ' FROM ' . implode(', ', $tables) . ' ';
        return $query;
    }

    /**
     * visszaadja az adatosztály alapján a select querystring azon szakaszát, mely az
     * ORDER BY paramétereit (attributum, irány) tartalmazza |
     * pl: ORDER BY p.name ASC
     * @return string az orderby queryszakasz
     * @todo több paraméter alapján rendezés
     */
    #[Pure] private function getOrderByQuery(): string
    {
        $query = '';
        $order = $this->source->getOrder();
        if ($order !== null) {
            $query .= 'ORDER BY ' . $order . ' ';
        }
        $orderDir = $this->source->getOrderDirection();
        if ($orderDir !== null) {
            $query .= $orderDir . ' ';
        }
        return $query;
    }

    /**
     * visszaadja az adatosztály alapján a select querystring azon szakaszát, mely az
     * mely az OFFSET-et és a LIMIT-et tartalmazza |
     * pl: 'LIMIT 10 5'
     * @return string
     */
    #[Pure] private function getLimitAndOffsetQuery(): string
    {
        $limit = $this->source->hasLimit();
        $offset = $this->source->hasOffset();
        if (!$offset && $limit) {
            return 'LIMIT ?';
        } else
            if ($offset) {
                return 'LIMIT ' . (($limit) ? '?' : '0') . ', ?';
            }
        return ' ';
    }

    /**
     * a megkapott querystring és a mentett adatForrás alapján lefuttatja a query-t
     * @param string $queryString query szövege
     * @return array a query eredménye
     */
    private function runQuery(string $queryString): array
    {
        $query = $this->pdo->prepare($queryString);
        $values = $this->source->getBindedValues();
        if (!empty($values)) {
            foreach ($values as $key => $value) {
                $id = ($value[2] !== null) ? $value[2] : $key + 1;
                $query->bindValue($id, $value[0], $value[1]);
            }
        }
        $query->execute();
        $rt = $this->fetchType;
        return $query->$rt($this->fetchMode);
    }

    /**
     * a megkapott querystring és a mentett adatForrás alapján lefuttatja a összdarabszám lekérdező query-t
     * @param string $queryString query szövege
     * @return int darabszám
     */
    private function runCountQuery(string $queryString): int
    {
        $query = $this->pdo->prepare($queryString);
        $values = $this->source->getBindedValues();
        if (!empty($values)) {
            $count = count($values) - $this->source->countOfActiveLimitAndOffset();
            for ($i = 0; $i < $count; $i++) {
                $query->bindValue($i + 1, $values[$i][0], $values[$i][1]);
            }
        }
        $query->execute();
        return (int)$query->fetch()['COUNT'];
    }

    /**
     * ha van al-lekérdezés az adatOsztályban összeállítj a query stringjét és visszadja
     * @return string al-query string
     */
    private function getSubQueryAsAttribute(): string
    {
        $query = '';
        $subQuery = $this->source->getSubQueryAsAttribute();
        if (count($subQuery) !== 0) {
            $query = ',';
            foreach ($subQuery as [$processor, $source, $alias]) {
                $processor->setSource($source);
                $query .= "(" . $processor->createQuery() . ') AS ' . $alias . ' ';
            }
        }
        return $query;
    }
}
