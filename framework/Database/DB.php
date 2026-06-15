<?php

namespace Framework\Database;

use Framework\Contracts\QueryBuilderInterface;

class DB {
    public static function table(string $table): QueryBuilderInterface {
        return ConnectionManager::table($table);
    }
    
    // Shorthand methods
    public static function select(string $query, array $bindings = []): array {
        $stmt = ConnectionManager::getConnection()->getPdo()->prepare($query);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }
    
    public static function insert(string $query, array $bindings = []): bool {
        $stmt = ConnectionManager::getConnection()->getPdo()->prepare($query);
        return $stmt->execute($bindings);
    }
    
    public static function update(string $query, array $bindings = []): bool {
        $stmt = ConnectionManager::getConnection()->getPdo()->prepare($query);
        return $stmt->execute($bindings);
    }
    
    public static function delete(string $query, array $bindings = []): bool {
        $stmt = ConnectionManager::getConnection()->getPdo()->prepare($query);
        return $stmt->execute($bindings);
    }
}