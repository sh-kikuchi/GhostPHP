<?php

namespace app\aura\database;

use PDO;
use PDOException;

/**
 * Class DataBaseConnect
 *
 * Manages the connection to the database using PDO.
 */
class DataBaseConnect {
    /**
     * @var PDO|null The PDO instance shared within a single request.
     */
    private static ?PDO $shared = null;

    private $pdo;

    /**
     * DataBaseConnect constructor.
     * Reuses the shared PDO connection, creating it on first use.
     */
    public function __construct() {
        // 同一リクエスト内では接続を1本に揃え、Serviceのトランザクションを全Repositoryに効かせる
        $this->pdo = self::$shared ??= $this->connect();
    }

    /**
     * Discards the shared PDO connection (mainly for tests).
     *
     * @return void
     */
    public static function reset(): void {
        self::$shared = null;
    }

    /**
     * Establishes a connection to the database.
     *
     * @return PDO The PDO instance for the database connection.
     * @throws PDOException If the connection fails.
     */
    private function connect() {
        $host = $_ENV['DB_HOST'];
        $db   = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];
       
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            return $pdo;
            
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }
    }

    /**
     * Returns the PDO instance.
     *
     * @return PDO The PDO instance.
     */
    public function getPDO() {
        return $this->pdo;
    }
}