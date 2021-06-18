<?php

namespace database;

use databaseSource\PDOQueryDataSource;
use simpleDatabaseProcessor\SimpleSelectPDOProcessor;
use complexDatabaseProcessor\PDOSelectProcessor;

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
        $proc = 'complexDatabaseProcessor\PDO' . $type . 'Processor';
        return [new $proc(PDOConnection::getInstance()), new PDOQueryDataSource()];
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
            $proc = '\simpleDatabaseProcessor\Simple' . $type . 'PDOProcessor';
        } else {
            $proc = '\complexDatabaseProcessor\ComplexPDO' . $type . 'Processor';
        }
        return new $proc(PDOConnection::getInstance());
    }

    /**
     * visszad egy PDO adatforrást
     * @return querySource\PDOQueryDataSource az adatforrás
     */
    public function getDataSource(): DPOQueryDataSource
    {
        return new querySource\PDOQueryDataSource();
    }
}