<?php
namespace Framework\Database;

use Framework\Contracts\ConnectionInterface;
use Framework\Contracts\QueryBuilderInterface;

class ConnectionManager {
    
    private static $connection;
    
    public static function setConnection(ConnectionInterface $connection): void {
        self::$connection = $connection;
    }
    
    public static function getConnection(): ConnectionInterface {
        if (self::$connection === null) {
            throw new \Exception('No database connection set.');
        }
        return self::$connection;
    }
    
    public static function table(string $table): QueryBuilderInterface {
        $connection = self::getConnection();
        return (new QueryBuilder($connection))->table($table);
    }
}