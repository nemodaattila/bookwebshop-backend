<?php

namespace databaseSource;

use exception\HttpResponseTriggerException;

/**
 * Class TablesAndAttributesClass class for storing the PDO query's tables and attributes
 * @package databaseSource
 */
class TablesAndAttributesClass
{
    /**
     * @var array array of tables and attributes, format:
     * (<table name>=>[alias=><alias name>, attributes=>[name=><attribute name>, alias=><aliasname>]])[]
     */
    private array $tables = [];

    /**
     * adding new table
     * if it not exists , sets alias and an empty attribute array
     * @param string $name table name
     * @param string|null $alias table alias
     * @example $x->addTable('person', 'p')
     */
    public function addTable(string $name, ?string $alias = null)
    {
        if (!array_key_exists($name, $this->tables)) {
            $this->tables[$name]['alias'] = $alias;
            $this->tables[$name]['attributes'] = [];
        }
    }

    /**
     * adding new attributes
     * @param string $tableName name of the table
     * @param array $attributes array of attributes, format: ((<alias name> | int) => <attribute name>)[]
     * @throws HttpResponseTriggerException if table not exists
     */
    public function addAttributes(string $tableName, array $attributes)
    {
        if (!array_key_exists($tableName, $this->tables)) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'PDOTACN'], 500);
        }
        foreach ($attributes as $key => $attribute) {
            if (is_int($key)) {
                $this->tables[$tableName]['attributes'][] = ['name' => $attribute, 'alias' => null];
            } else
                $this->tables[$tableName]['attributes'][] = ['name' => $key, 'alias' => $attribute];
        }
    }

    /**
     * return all attributes
     * @return array array of attributes
     */
    public function getAll(): array
    {
        return $this->tables;
    }
}
