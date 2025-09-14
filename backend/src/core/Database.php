<?php

namespace core;

use PDOException;
use PDO;

/**
 * Class to return the connection to the database, the database connection use the Singleton pattern
 *
 * @author Vitor Carvalho vitorcarvalhodso@gamil.com
 */
class Database {
    private static $instance = null;
    private PDO $pdo;

    private function __construct() {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $db   = getenv('DB_DATABASE');
        $user = getenv('DB_USERNAME');
        $pass = getenv('DB_PASSWORD');
        $charset = getenv('DB_CHARSET') ?: 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('DB connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Returns the Database object instance
     *
     * @return Database connection
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Returns the connection
     *
     * @return PDO
     */
    public function getConnection(): PDO {
        return $this->pdo;
    }
}
