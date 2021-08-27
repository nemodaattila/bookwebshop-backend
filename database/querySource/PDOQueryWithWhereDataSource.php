<?php

namespace databaseSource;

use exception\HttpResponseTriggerException;
use interfaces\IPDOQueryProcessorInterface;

abstract class PDOQueryWithWhereDataSource extends PDOQueryDataSourceParent
{
    /**
     * @var array sub queries as attributes | array of PDOQueryDataSource
     */
    protected array $subQueryAsAttribute = [];
    /**
     * @var WhereConditionsBackboneClass collector class of where conditions
     */
    protected WhereConditionsBackboneClass $whereConditions;

    public function __construct()
    {
        parent::__construct();
        $this->whereConditions = new WhereConditionsBackboneClass();
    }

    /**
     * add sub query as attribute
     * @param IPDOQueryProcessorInterface $pdoProcessor a query processor class
     * @param PDOSelectQueryDataSource $dataSource - data source
     * @param string $alias alias for sub - query
     * @throws HttpResponseTriggerException if there is no alias
     */
    public function addSubQueryAsAttribute(IPDOQueryProcessorInterface $pdoProcessor, PDOSelectQueryDataSource $dataSource, string $alias)
    {
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
     * returns the query string of the where conditions
     * @return string complied query string
     */
    public function getQueryWhere(): string
    {
        return $this->whereConditions->getQueryString();
    }
}
