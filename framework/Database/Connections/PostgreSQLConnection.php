<?php
namespace Framework\Database\Connections;

use Framework\Contracts\ConnectionInterface;
use PDO;

class PostgreSQLConnection implements ConnectionInterface {
    
    private $pdo;
    
    public function __construct(array $config) {
        $dsn = sprintf(
            "pgsql:host=%s;dbname=%s",
            $config['host'],
            $config['database']
        );
        
        $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    
    public function getPdo(): PDO {
        return $this->pdo;
    }
}