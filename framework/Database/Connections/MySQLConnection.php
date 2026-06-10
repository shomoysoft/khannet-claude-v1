<?php
namespace App\Database\Connections;

use App\Contracts\ConnectionInterface;
use PDO;
use PDOException;

class MySQLConnection implements ConnectionInterface {
    
    private $pdo;
    
    public function __construct(array $config) {
        try {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                $config['host'],
                $config['database'],
                $config['charset'] ?? 'utf8mb4'
            );
            
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            throw new \Exception("MySQL connection failed: " . $e->getMessage());
        }
    }
    
    public function getPdo(): PDO {
        return $this->pdo;
    }
}