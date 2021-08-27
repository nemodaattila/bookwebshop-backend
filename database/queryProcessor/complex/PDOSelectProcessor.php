<?php

namespace complexDatabaseProcessor;

use databaseSource\PDOSelectQueryDataSource;
use exception\HttpResponseTriggerException;
use PDO;

/**
 *
 * Class ComplexPDOSelectProcessor class for creating and running PDO Select queries
 * @package complexDatabaseProcessor
 */
class PDOSelectProcessor extends PDOQueryProcessorParent
{
    /**
     * @var string fetching type of query result |
     * fetch or fetchAll
     */
    protected string $fetchType = 'fetchAll';
    /**
     * @var int fetch method of query result |
     * pl: PDO::FETCH_ASSOC, PDO::FETCH_ARRAY
     */
    protected int $fetchMode = PDO::FETCH_ASSOC;

    /**
     * saving and running PDO query
     * @param PDOSelectQueryDataSource $source - query datasource
     * @param string $fetchType - fetch type
     * @param int $fetchMode - fetch method
     * @return array|bool result of query
     * @throws HttpResponseTriggerException if the fetch type is not fetch or fetchAll
     * @example selectProc->query($source, 'fetch', PDO::FETCH_LAZY)
     */
    public function query(PDOSelectQueryDataSource $source, string $fetchType = 'fetchAll', int $fetchMode = PDO::FETCH_ASSOC): array|bool
    {
        $this->setSource($source);
        $this->fetchMode = $fetchMode;
        if (!in_array($fetchType, ['fetch', 'fetchAll']))
            throw new HttpResponseTriggerException(false, ['errorCode' => 'PDOFTBT'], 500);
        $this->fetchType = $fetchType;
        return $this->runQuery($this->createQuery());
    }

    /**
     * runs a query from a querystring with the saved values (boundValues)
     * @param string $queryString query string
     * @return array result of query
     */

    /**
     * creates and returns a query string from query data source
     * @return string query string
     */
    public function createQuery(): string
    {
        $query = 'SELECT ' . $this->getTableAndAttributesQuery();
        $where = $this->source->getQueryWhere();
        if ($where !== '')
            $query .= ' WHERE ' . $where;
//        $query .= $this->getGroupQuery();
        $query .= $this->getOrderByQuery();
        $query .= $this->getLimitAndOffsetQuery();
        return trim(str_replace('  ', ' ', $query));
    }

    /**
     * returns the part of the query which contains the attributes and tables (p.id, p.name from person as p)
     * @return string query part
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
     * returns the part of the query which contains the order and the order direction parameters
     * eg: ORDER BY p.name ASC
     * @return string query part
     * TODO több paraméter alapján rendezés
     */
    private function getOrderByQuery(): string
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
     * returns the part of the query which contains the OFFSET and LIMIT parameters
     * e.g.: 'LIMIT 10 5'
     * @return string query part
     */
    private function getLimitAndOffsetQuery(): string
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
     * runs a query that results in count number, an earlier data source can be used
     * @param PDOSelectQueryDataSource|null $source query data source
     * @return int count - tha result of the query
     * @throws HttpResponseTriggerException if the data source is null
     */
    public function countQuery(?PDOSelectQueryDataSource $source = null): int
    {
        if ($source !== null)
            $this->setSource($source);
        if ($this->source !== null) {
            return $this->runCountQuery($this->createCountQuery());
        }
        throw new HttpResponseTriggerException(false, ['errorCode' => 'PDOSPCQSE'], 500);
    }

    /**
     * runs a counter query from a querystring with the saved values (boundValues)
     * @param string $queryString query string
     * @return int count
     */
    private function runCountQuery(string $queryString): int
    {
        $query = $this->pdo->prepare($queryString);
        $values = $this->source->getBoundValues();
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
     * creates and returns a counter query string from query data source
     * @return string query string
     */
    private function createCountQuery(): string
    {
        $query = 'SELECT COUNT(*) AS COUNT FROM ( SELECT ' . $this->getTableAndAttributesQuery();
        $where = $this->source->getQueryWhere();
        if ($where !== '')
            $query .= ' WHERE ' . $where;
        $query .= ') AS COUNTSUB';
        return trim(str_replace('  ', ' ', $query));
    }
}
