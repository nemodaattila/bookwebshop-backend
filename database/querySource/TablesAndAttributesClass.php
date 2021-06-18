<?php

namespace databaseSource;


/**
 * Class TablesAndAttributesClass PDO lekérdezéshez tárolja a táblaneveket és attributumokat
 * @package core\backend\database\querySource
 */
class TablesAndAttributesClass
{
    /**
     * @var array táblák és attributumok tömbje: formátum:
     * (<táblanév>=>[alias=><aliasnév>, attributes=>[name=><attributenév>, alias=><aliasnév>]])[]
     */
    private array $tables = [];

    /**
     * új tábla hozzáadása - ha még nem létezik létrehozza, beállítja az aliast, és üres attributum tömböt hoz létre
     * @param string $name tábla neve
     * @param string|null $alias tábla aliasa
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
     * új attribute hozzáadása
     * @param string $tableName a tábla neve, korábban fel kell venni addTable-vel
     * @param array $attributes attributumok tömbje, formátum: ((<aliasNév> | int) => <attributeNév>)[]
     * @throws RequestResultException ha a tableName nem létezik
     */
    public function addAttributes(string $tableName, array $attributes)
    {
        if (!array_key_exists($tableName, $this->tables)) {
            throw new RequestResultException(400, ['errorCode' => 'PDOTACN']);
        }
        foreach ($attributes as $key => $attribute) {
            if (is_int($key)) {
                $this->tables[$tableName]['attributes'][] = ['name' => $attribute, 'alias' => null];
            } else
                $this->tables[$tableName]['attributes'][] = ['name' => $key, 'alias' => $attribute];
        }
    }

    /**
     * visszaadja a teljes attributum array-t
     * @return array - attributumok tömbje
     */
    public function getAll(): array
    {
        return $this->tables;
    }
}
