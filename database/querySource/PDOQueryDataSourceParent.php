<?php

namespace databaseSource;

use exception\HttpResponseTriggerException;
use PDO;

abstract class PDOQueryDataSourceParent
{
    /**
     * @var TablesAndAttributesClass class which contains the tables and attributes of the query
     */
    protected TablesAndAttributesClass $tablesAndAttributes;

    /**
     * @var array values to be bound in the query
     */
    protected array $boundValues = [];

    public function __construct()
    {
        $this->tablesAndAttributes = new TablesAndAttributesClass();
    }

    public function getBoundValues(): array
    {
        return $this->boundValues;
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
     * checks if the given attribute's table exists
     * if exists returns it with alias name
     * if not exception
     * if no table name was added, simply returns it
     * @param string $attribute az attribute: person.name
     * @return string attribute with table alias, or simply the attribute
     * @throws HttpResponseTriggerException if table doesn't exist
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
     * adds new table to the data source
     * @param string $name table name
     * @param string|null $alias table alias
     * @example $dataSource->addTable('person','p');
     */
    public function addTable(string $name, ?string $alias = null)
    {
        $this->tablesAndAttributes->addTable($name, $alias);
    }

    /**
     * adding attributes to datasource
     * @param string $tableName full name of table
     * @param array $attributes attributes
     * @throws HttpResponseTriggerException if table not exists
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
}
