<?php

namespace Framework\Database;

use PDO;
use PDOException;
use Framework\Support\Config;

class Connection
{
    private static $pdo;

    public static function getInstance()
    {
        if (self::$pdo === null) {
            $config = Config::get('database');
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']}";

            try {
                self::$pdo = new PDO($dsn, $config['user'], $config['password']);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // In a real application, you would log this error
                die("Could not connect to the database: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public static function resetInstance()
    {
        self::$pdo = null;
    }
}