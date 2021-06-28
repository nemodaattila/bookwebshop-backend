<?php

namespace database;

use PDO;

/**
 * Class for creating PDO connection
 * @package database
 */
final class PDOConnection
{
    /**
     * @var PDO|null PDO singleton instance
     */
    private static ?PDO $dbSingleton = null;

    /**
     * returns a PDO, if it not exists, creates itr
     * @return PDO PDO connection
     */
    public static function getInstance(): PDO
    {
        if (self::$dbSingleton == null) {

            self::$dbSingleton = self::createPDO();
        }
        return self::$dbSingleton;
    }

    /**
     * creates a PDO instance based on the data in config/PDOConfig.php
     * @return PDO PDO connection
     */
    private static function createPDO(): PDO
    {
        $config = parse_ini_file('.\config\PDOConfig.php');
        return new PDO("mysql:host=" . $config['dbHost'] . ";dbname=" . $config['dbName']
            . ";charset=utf8", $config['dbUser'], $config['dbPassword'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }
}
