<?php

namespace Leart\JsonDerulo\Services;

use PDO;
use PDOException;

/**
 * DatabaseService — PDO Singleton for MySQL
 *
 * Reads connection settings from $_ENV (loaded by phpdotenv).
 * Uses UTF8MB4, exception error mode, and prepared statements only.
 */
class DatabaseService
{
    private static ?PDO $instance = null;

    /**
     * Get the shared PDO connection.
     *
     * @return PDO
     * @throws PDOException if connection fails
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $name = $_ENV['DB_NAME'] ?? 'elevura_ai';
            $user = $_ENV['DB_USER'] ?? 'root';
            $pass = $_ENV['DB_PASS'] ?? '';

            $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'",
            ]);
        }

        return self::$instance;
    }

    /**
     * Prevent cloning and unserialization of the singleton.
     */
    private function __construct() {}
    private function __clone() {}
    public function __wakeup() { throw new \Exception('Cannot unserialize singleton'); }
}
