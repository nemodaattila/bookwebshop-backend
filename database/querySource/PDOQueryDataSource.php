<?php

namespace databaseSource;


/**
 * Class PDOQueryDataSource adatforrás PDO komplex PDO műveletekhet
 * @package core\backend\database\querySource
 * @see PDOQueryProcessorParent
 */
class PDOQueryDataSource
{
    /**
     * @var TablesAndAttributesClass objektum mely a lekérdezés tábláit és attributumait tartalmazza
     */
    private TablesAndAttributesClass $tablesAndAttributes;
    /**
     * @var bool van e bállitva limit
     */
    private bool $hasLimit = false;
    /**
     * @var bool van e beállitva offset
     */
    private bool $hasOffset = false;
    /**
     * @var array allekérdezések mint attributum | array of PDOQueryDataSource
     */
    private array $subQueryAsAttribute = [];
    /**
     * @var WhereConditionsBackboneClass where feltételek gyüjtőosztálya
     */
    private WhereConditionsBackboneClass $whereConditions;
    /**
     * @var array lekérdezésnek megadandó értékek
     */
    private array $bindedValues = [];
    /**
     * @var string ORDER BY attributum
     * @todo több paraméter megadása
     */
    private string $order;
    /**
     * @var string rendezés iránya ASC/DESC
     */
    private string $orderDirection;
    /**
     * @var bool SELECT DISTINCT engedélyezése
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

    public function getBindedValues(): array
    {
        return $this->bindedValues;
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

    public function setOrder(string $order): void
    {
        $order = $this->checkTableExists($order);
        $this->order = $order;
    }

    public function setOrderDirection(string $orderDirection): void
    {
        $this->orderDirection = $orderDirection;
    }

    #[Pure] public function __construct()
    {
        $this->tablesAndAttributes = new TablesAndAttributesClass();
        $this->whereConditions = new WhereConditionsBackboneClass();
    }

    /**
     * limit engedélyezése
     */
    public function enableLimit()
    {
        $this->hasLimit = true;
    }

    /**
     * offset engedélyezése
     */
    public function enableOffset()
    {
        $this->hasOffset = true;
    }

    /**
     * egy érték hozzáadása későbbi bindelésre
     * @param mixed $value mentendő érték
     * @param int|null $bindType az érték tipusa | default PDO::PARAM_STR
     * @param string|null $bindName placeholdername - még nem működuk !
     * @example $dataSource->bindValue(1, PDO::PARAM_INT);
     */
    public function bindValue(mixed $value, ?int $bindType = PDO::PARAM_STR, ?string $bindName = null)
    {
        $this->bindedValues[] = [$value, $bindType, $bindName];
    }

    /**
     * visszadja a limitet és az offsetet mint darabszámot (bool -> int)
     * ez összdarabszám lekérdező függvénynél fontos, mivel ott nem kell ezeket bindelni
     * @return int darabszám
     */
    public function countOfActiveLimitAndOffset(): int
    {
        return (int)$this->hasOffset + (int)$this->hasLimit;
    }

    /**
     * al-lekérdezés (mint attributum) hozzáadása
     * @param IPDOQueryProcessorInterface $pdoProcessor a feldolgozó osztály
     * @param PDOQueryDataSource $dataSource - adatforrás
     * @param string $alias alias az allekérdezésnek
     * @throws RequestResultException ha nincs alias
     */
    public function addSubQueryAsAttribute(IPDOQueryProcessorInterface $pdoProcessor, PDOQueryDataSource $dataSource, string $alias)
    {
        if ($alias === null) {
            throw new RequestResultException(500, ['errorCode' => 'PDOASQA']);
        }
        $this->subQueryAsAttribute[] = [$pdoProcessor, $dataSource, $alias];
    }

    /**
     * visszaadja az al-lekérdezések objektumait
     * @return array a lekérdezések tömbje
     */
    public function getSubQueryAsAttribute(): array
    {
        return $this->subQueryAsAttribute;
    }

    /**
     * tábla hozzáadása az adatforráshoz
     * @param string $name a tábla neve
     * @param string $alias tábla aliasa
     * @example $dataSource->addTable('person','p');
     */
    public function addTable(string $name, string $alias)
    {
        $this->tablesAndAttributes->addTable($name, $alias);
    }

    /**
     * attributumok hozzáadaása adatforráshoz
     * @param string $tableName a tábla teljes neve
     * @param array $attributes attributumok
     * @throws RequestResultException ha a tábla korábban nem lett felvéve
     * @example $dataSource->addAttributes('book',['isbn']);
     */
    public function addAttributes(string $tableName, array $attributes)
    {
        $this->tablesAndAttributes->addAttributes($tableName, $attributes);
    }

    /**
     * visszad minden hozzáadott táblát és attributumot
     * @return array
     */
    #[Pure] public function getTablesAndAttributes(): array
    {
        return $this->tablesAndAttributes->getAll();
    }

    /**
     * WHERE feltétel hozzáadása az adatforráshoz
     * @param string $type feltétel operátora pl: '=' , 'LIKE'
     * @param mixed $parameters paraméterek, lehet array: ['person.id', '?'] vagy egy al-feltétel (IPDOWhereConditionInterface)
     * @param null $conditionOperator 2 feltétel közti operátor - (a=1 AND b=2) az első feltétel hozzádásakor nem veszi figyelembe
     * @param false $isBracketed ha true zárójelbe rakja
     * @throws RequestResultException pl. a többféle hiba lehet: ha a where paraméter nem szerepel már megadott táblában, vagy a kondiciók száma nem megfelelő
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
     * al-feltétel hozzáadása az adatforráshoz
     * @param WhereConditionsBackboneClass $class az alfeltétel objektum
     * @param null $conditionOperator 2 feltétel közti operátor
     * @param false $isBracketed - zárójelezett-e
     * @todo tesztelni
     */
    public function addConditionObject(WhereConditionsBackboneClass $class, $conditionOperator = null, bool $isBracketed = false)
    {
        $this->whereConditions->addConditionObject($class, $conditionOperator, $isBracketed);
    }

    /**
     * ellenőrzi hogy a megadott attributum táblája szerepel a táblák között, ha igen aliasnévvel visszadja, egyébként hibaüzenet
     * ha az attributum táblanév nélküli, szimplán visszadja
     * @param string $attribute az attributumnév: person.name
     * @return string ha meg van adva táblanév (person.id) akkor aliassal: (p.id), ha nincs (id), változatlan
     * @throws RequestResultException ha meg van adva táblanév, de a tábla nem létezik
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
                throw new RequestResultException(500, ['errorcode' => 'QSTACNE', 'value' => $attribute]);
            }
        }
        return $newName;
    }

    /**
     * visszadja a WHERE feltétekből összeállított query-stringet
     * @return string az összeállított  query string
     */
    public function getQueryWhere(): string
    {
        return $this->whereConditions->getQueryString();
    }
}
