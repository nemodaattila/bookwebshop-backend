<?php

namespace complexDatabaseProcessor;

use databaseSource\PDOUpdateQueryDataSource;

class PDOUpdateProcessor extends PDOQueryProcessorParent
{

    public function query(PDOUpdateQueryDataSource $dataSource): bool
    {
        $this->setSource($dataSource);
        $this->createQuery();
        return $this->runQuery($this->createQuery());
    }

    public function createQuery(): string
    {
        $query = 'Update ' . $this->getTableAndAttributesQuery();
        $where = $this->source->getQueryWhere();
        if ($where !== '')
            $query .= ' WHERE ' . $where;

        return trim(str_replace('  ', ' ', $query));
    }

    private function getTableAndAttributesQuery(): string
    {
        $params = ($this->source->getTablesAndAttributes());
        $table = array_key_first($params);
        $params = (array_shift($params))['attributes'];
        $query = $table . ' SET ';
        $attribs = [];
        foreach ($params as $value) {
            $attribs[] = $value['name'] . '= ? ';
        }
        $query .= implode(', ', $attribs);
        return $query;
    }

}
