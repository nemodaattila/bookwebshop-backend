<?php

namespace databaseSource;

use exception\HttpResponseTriggerException;
use interfaces\IPDOQueryProcessorInterface;
use PDO;

/**
 * Class PDOQueryDataSource datasource for complex PDO queries
 * @package databaseSource
 * @see PDOQueryProcessorParent
 */
class PDOQueryDataSource
{
    /**
     * @var TablesAndAttributesClass class which contains the tables and attributes of the query
     */
    private TablesAndAttributesClass $tablesAndAttributes;
    /**
     * @var bool limit is enabled
     */
    private bool $hasLimit = false;
    /**
     * @var bool offset is enabled
     */
    private bool $hasOffset = false;
    /**
     * @var array sub queries as attributes | array of PDOQueryDataSource
     */
    private array $subQueryAsAttribute = [];
    /**
     * @var WhereConditionsBackboneClass collector class of where conditions
     */
    private WhereConditionsBackboneClass $whereConditions;
    /**
     * @var array values to be bound in the query
     */
    private array $boundValues = [];
    /**
     * @var string ORDER BY attribute
     * @todo több paraméter megadása
     */
    private string $order;
    /**
     * @var string direction of the order ASC/DESC
     */
    private string $orderDirection;
    /**
     * @var bool enable SELECT DISTINCT
     */
    private bool $distinct = false;

    //getters/setters
    public function isDistinct(): bool
    {
        return $this->distinct;
    }

    public function setDistinct()
    {
        $this->distinct = true;
    }

    public function hasLimit(): bool
    {
        return $this->hasLimit;
    }

    public function hasOffset(): bool
    {
        return $this->hasOffset;
    }

    public function getBoundValues(): array
    {
        return $this->boundValues;
    }

    public function getOrder(): ?string
    {
        if (isset($this->order))
            return $this->order;
        return null;
    }

    public function getOrderDirection(): ?string
    {
        if (isset($this->orderDirection))
            return $this->orderDirection;
        return null;
    }

    /**
     * sets the order parameter
     * @throws HttpResponseTriggerException if table of the order parameter not exists
     */
    public function setOrder(string $order): void
    {
        $order = $this->checkTableExists($order);
        $this->order = $order;
    }

    public function setOrderDirection(string $orderDirection): void
    {
        $this->orderDirection = $orderDirection;
    }

    public function __construct()
    {
        $this->tablesAndAttributes = new TablesAndAttributesClass();
        $this->whereConditions = new WhereConditionsBackboneClass();
    }

    /**
     * enable limit
     */
    public function enableLimit()
    {
        $this->hasLimit = true;
    }

    /**
     * enable offset
     */
    public function enableOffset()
    {
        $this->hasOffset = true;
    }

    /**
     * add value to be bound in query
     * @param mixed $value the value
     * @param int|null $bindType type of the value | default PDO::PARAM_STR
     * @param string|null $bindName placeholderName - not yet working !
     * @example $dataSource->bindValue(1, PDO::PARAM_INT);
     */
    public function bindValue(mixed $value, ?int $bindType = PDO::PARAM_STR, ?string $bindName = null)
    {
        $this->boundValues[] = [$value, $bindType, $bindName];
    }

    /**
     * returns offset and limit as count (bool -> int) (true, true) -> 2
     * @return int count
     */
    public function countOfActiveLimitAndOffset(): int
    {
        return (int)$this->hasOffset + (int)$this->hasLimit;
    }

    /**
     * add sub query as attribute
     * @param IPDOQueryProcessorInterface $pdoProcessor a query processor class
     * @param PDOQueryDataSource $dataSource - data source
     * @param string $alias alias for sub - query
     * @throws HttpResponseTriggerException if there is no alias
     */
    public function addSubQueryAsAttribute(IPDOQueryProcessorInterface $pdoProcessor, PDOQueryDataSource $dataSource, string $alias)
    {
        if ($alias === null) {
            throw new  HttpResponseTriggerException(false, ['errorCode' => 'PDOASQA'], 500);
        }
        $this->subQueryAsAttribute[] = [$pdoProcessor, $dataSource, $alias];
    }

    /**
     * return sub-queries
     * @return array array of sub queries
     */
    public function getSubQueryAsAttribute(): array
    {
        return $this->subQueryAsAttribute;
    }

    /**
     * adds new table to the data source
     * @param string $name table name
     * @param string $alias table alias
     * @example $dataSource->addTable('person','p');
     */
    public function addTable(string $name, string $alias)
    {
        $this->tablesAndAttributes->addTable($name, $alias);
    }

    /**
     * adding attributes to datasource
     * @param string $tableName full name of table
     * @param array $attributes attributes
     * @example $dataSource->addAttributes('book',['isbn']);
     */
    public function addAttributes(string $tableName, array $attributes)
    {
        $this->tablesAndAttributes->addAttributes($tableName, $attributes);
    }

    /**
     * return all saved tables and attributes
     * @return array
     */
    public function getTablesAndAttributes(): array
    {
        return $this->tablesAndAttributes->getAll();
    }

    /**
     * add where condition to the database
     * @param string $type condition operator pl: '=' , 'LIKE'
     * @param mixed $parameters parameters, can be array: ['person.id', '?'] or a sub-query (IPDOWhereConditionInterface)
     * @param null $conditionOperator operator between 2 conditions - (a=1 AND b=2)
     * @param false $isBracketed if true the condition will be bracketed
     * @throws HttpResponseTriggerException if on of the parameter's table not exists
     * @example $dataSource->addWhereCondition('=',['person.id', '?'],'AND');
     */
    public function addWhereCondition(string $type, mixed $parameters, $conditionOperator = null, bool $isBracketed = false)
    {
        if (is_array($parameters)) {
            foreach ($parameters as $key => $param) {
                if (is_string($param))
                    $parameters[$key] = $this->checkTableExists($param);
            }
        }
        $this->whereConditions->addWhereCondition($type, $parameters, $conditionOperator, $isBracketed);
    }

    /**
     * add qub query to the data source
     * @param WhereConditionsBackboneClass $class condition class
     * @param null $conditionOperator operator between 2 condition
     * @param false $isBracketed if true the condition will be bracketed
     */
    public function addConditionObject(WhereConditionsBackboneClass $class, $conditionOperator = null, bool $isBracketed = false)
    {
        $this->whereConditions->addConditionObject($class, $conditionOperator, $isBracketed);
    }

    /**
     * checks if the given attribute's table exists
     * if exists returns it with alias name
     * if not exception
     * if no table name was added, simply returns ot
     * @param string $attribute az attribute: person.name
     * @return string attribute with table alias, or simply the attribute
     * @throws HttpResponseTriggerException if table doesn't exists
     */
    public function checkTableExists(string $attribute): string
    {
        $newName = $attribute;
        $explodedAttrib = explode('.', $attribute);
        if (count($explodedAttrib) === 2) {
            $tables = $this->tablesAndAttributes->getAll();
            if (array_key_exists($explodedAttrib[0], $tables)) {
                $alias = $tables[$explodedAttrib[0]]['alias'];
                if ($alias !== null) {
                    $newName = $alias . '.' . $explodedAttrib[1];
                }
            } else {
                throw new HttpResponseTriggerException(false, ['errorCode' => 'QSTACNE', 'value' => $attribute], 400);
            }
        }
        return $newName;
    }

    /**
     * returns the query string of the where conditions
     * @return string complied query string
     */
    public function getQueryWhere(): string
    {
        return $this->whereConditions->getQueryString();
    }
}
