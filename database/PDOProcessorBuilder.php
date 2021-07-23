<?php

namespace database;

use databaseSource\PDOQueryDataSource;
use exception\HttpResponseTriggerException;
use interfaces\IPDOQueryProcessorInterface;
use simpleDatabaseProcessor\SimpleInsertPDOProcessor;
use simpleDatabaseProcessor\SimplePDOProcessorParent;
use simpleDatabaseProcessor\SimpleSelectPDOProcessor;
use simpleDatabaseProcessor\SimpleUpdatePDOProcessor;
use simpleDatabaseProcessor\SimpleDeletePDOProcessor;

/**
 * Class PDOProcessorBuilder builder class for creating the appropriate PDO helper class
 * @package database
 */
final class PDOProcessorBuilder
{
    /**
     * return a complex PDO handler class, and a PDO data source, based on parameters
     * @param string $type type of class/method ['Insert','Select','Update','Delete']
     * @return array [IPDOQueryProcessorInterface <queryHelper class instance>, PDOQueryDataSource <datasource>]
     * @throws HttpResponseTriggerException if the type not: 'Insert','Select','Update' or 'Delete'
     */
    public static function getProcessorAndDataSource(string $type): array
    {
        $type = ucfirst(strtolower($type));
        if (!in_array($type, ['Insert', 'Select', 'Update', 'Delete'])) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'PDOPBBP'], 500);
        }
        $proc = 'complexDatabaseProcessor\PDO' . $type . 'Processor';
        return [new $proc(PDOConnection::getInstance()), new PDOQueryDataSource()];
    }

    /**
     * return a PDO handler class, based on parameter
     * @param string $type type of class/method ['Insert','Select','Update','Delete']
     * @param bool $simple is simplified , if true: namespace simpleDatabaseProcessor, if false: namespace complexDatabaseProcessor
     * @return IPDOQueryProcessorInterface|SimpleSelectPDOProcessor|SimpleUpdatePDOProcessor|SimpleInsertPDOProcessor PDO handler class
     * @throws HttpResponseTriggerException if the type not: 'Insert','Select','Update' or 'Delete
     */
    public static function getProcessor(string $type, bool $simple = false):
    IPDOQueryProcessorInterface|SimplePDOProcessorParent
    {
        $type = ucfirst(strtolower($type));
        if (!in_array($type, ['Insert', 'Select', 'Update', 'Delete'])) {
            throw new HttpResponseTriggerException(400, ['errorCode' => 'PDOPBBP']);
        }
        if ($simple) {
            $proc = '\simpleDatabaseProcessor\Simple' . $type . 'PDOProcessor';
        } else {
            $proc = '\complexDatabaseProcessor\ComplexPDO' . $type . 'Processor';
        }
        return new $proc(PDOConnection::getInstance());
    }

    /**
     * return a PDO data source
     * @return PDOQueryDataSource data source
     */
    public function getDataSource(): PDOQueryDataSource
    {
        return new PDOQueryDataSource();
    }
}
