<?php
namespace App\Database\Connections;

use App\Contracts\ConnectionInterface;
use PDO;

class SQLiteConnection implements ConnectionInterface {
    
    private $pdo;
    
    public function __construct(array $config) {
        $database = $config['database'] ?? ':memory:';
        
        $this->pdo = new PDO("sqlite:$database", null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    
    public function getPdo(): PDO {
        return $this->pdo;
    }
}
