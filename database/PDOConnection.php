<?php

namespace core\backend\database;

use PDO;

/**
 * Class PDOConnection osztály PDO kapcsolat létesítéséhez
 * @package backend
 */
final class PDOConnection
{
    /**
     * @var PDO|null PDO egyke példánya
     */
    private static ?PDO $dbSingleton = null;

    /**
     * visszaad egy PDO-t, ha nincs létrehozza
     * @return PDO a visszadott PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$dbSingleton == null) {

            self::$dbSingleton = self::createPDO();
        }
        return self::$dbSingleton;
    }

    /**
     * létrehoz egy PDO pélányt a \project\config\PDOconfig.php alapján
     * @return PDO PDO kapcsolat
     */
    private static function createPDO(): PDO
    {
        $config = parse_ini_file(ROOT . '\project\config\PDOConfig.php');
        return new PDO("mysql:host=" . $config['dbHost'] . ";dbname=" . $config['dbName']
            . ";charset=utf8", $config['dbUser'], $config['dbPassword'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }
}
