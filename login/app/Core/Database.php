<?php

declare(strict_types=1);

namespace Palmed\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::connect();
        }
        return self::$instance;
    }

    public static function connect(array $dbConfig = null): PDO
    {
        $db = $dbConfig ?? [
            'host' => config('database.host'),
            'port' => config('database.port', 3306),
            'name' => config('database.name'),
            'user' => config('database.user'),
            'password' => config('database.password'),
            'charset' => config('database.charset', 'utf8mb4'),
        ];

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $db['host'],
            $db['port'],
            $db['name'],
            $db['charset']
        );

        try {
            $pdo = new PDO($dsn, $db['user'], $db['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    public static function testConnection(array $dbConfig): bool
    {
        try {
            $pdo = self::connect($dbConfig);
            $pdo->query('SELECT 1');
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
