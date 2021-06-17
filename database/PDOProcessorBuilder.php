<?php

namespace core\backend\database;

use core\backend\database\queryProcessor\simple\SimpleInsertPDOProcessor;
use core\backend\database\queryProcessor\simple\SimpleSelectPDOProcessor;
use core\backend\database\queryProcessor\simple\SimpleUpdatePDOProcessor;
use core\backend\database\querySource\PDOQueryDataSource;
use core\backend\interfaces\IPDOQueryProcessorInterface;
use core\backend\model\RequestResultException;
use JetBrains\PhpStorm\Pure;

/**
 * Class PDOProcessorBuilder builder osztály a megfelelő PDO segítő osztály létrehozásához és visszadásához
 * @package backend
 */
final class PDOProcessorBuilder
{
    /**
     * visszaa egy, a paraméternek megfelelő komplex PDO query kezelő osztályt, és egy PDO adatforrást
     * @param string $type a kért osztály/művelet tipusa ['Insert','Select','Update','Delete']
     * @return array [IPDOQueryProcessorInterface <queryHelper class példány>, PDOQueryDataSource <adatforrás>]
     * @throws RequestResultException ha a $type nem valamelyik: ['Insert','Select','Update','Delete']
     */
    public static function getProcessorAndDataSource(string $type): array
    {
        $type = ucfirst(strtolower($type));
        if (!in_array($type, ['Insert', 'Select', 'Update', 'Delete'])) {
            throw new RequestResultException(400, ['errorcode' => 'PDOPBBP']);
        }
        $proc = 'core\backend\database\queryProcessor\complex\PDO' . $type . 'Processor';
        return [new $proc(PDOConnection::getInstance()), new querySource\PDOQueryDataSource()];
    }

    /**
     * visszad egy, a paraméternek megfelelő komplex PDO query kezelő osztályt
     * @param string $type az osztály/művelet tipusa ['Insert','Select','Update','Delete']
     * @param bool $simple ha true az osztály egyszerűsített lesz (\database\queryProcessor\simple)
     *  ha false akkor komplex (\database\queryProcessor\complex)
     * @return IPDOQueryProcessorInterface|SimpleSelectPDOProcessor|SimpleUpdatePDOProcessor|SimpleInsertPDOProcessor a PDO kezelő osztály
     * @throws RequestResultException ha a $type nem valamelyik: ['Insert','Select','Update','Delete']
     */
    public static function getProcessor(string $type, bool $simple = false):
    IPDOQueryProcessorInterface|SimpleSelectPDOProcessor|SimpleUpdatePDOProcessor|SimpleInsertPDOProcessor
    {
        $type = ucfirst(strtolower($type));
        if (!in_array($type, ['Insert', 'Select', 'Update', 'Delete'])) {
            throw new RequestResultException(400, ['errorcode' => 'PDOPBBP']);
        }
        if ($simple) {
            $proc = 'core\backend\database\queryProcessor\simple\Simple' . $type . 'PDOProcessor';
        } else {
            $proc = 'core\backend\database\queryProcessor\complex\PDO' . $type . 'Processor';
        }
        return new $proc(PDOConnection::getInstance());
    }

    /**
     * visszad egy PDO adatforrást
     * @return querySource\PDOQueryDataSource az adatforrás
     */
    #[Pure] public function getDataSource(): PDOQueryDataSource
    {
        return new querySource\PDOQueryDataSource();
    }
}
