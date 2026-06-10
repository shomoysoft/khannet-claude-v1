<?php
namespace App\Database;

use App\Contracts\ConnectionInterface;
use App\Contracts\QueryBuilderInterface;

class QueryBuilder implements QueryBuilderInterface {
    
    protected $connection;
    protected $table;
    protected $columns = ['*'];
    protected $wheres = [];
    protected $orderBys = [];
    protected $limitValue;
    protected $offsetValue;
    protected $bindings = [];
    
    public function __construct(ConnectionInterface $connection) {
        $this->connection = $connection;
    }
    
    public function table(string $table): self {
        $this->table = $table;
        return $this;
    }
    
    public function select(array $columns = ['*']): self {
        $this->columns = $columns;
        return $this;
    }
    
    public function where(string $column, string $operator, $value = null): self {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $this->wheres[] = [
            'type' => 'AND',
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];
        
        return $this;
    }
    
    public function orWhere(string $column, string $operator, $value = null): self {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $this->wheres[] = [
            'type' => 'OR',
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];
        
        return $this;
    }
    
    public function orderBy(string $column, string $direction = 'ASC'): self {
        $this->orderBys[] = ['column' => $column, 'direction' => strtoupper($direction)];
        return $this;
    }
    
    public function limit(int $limit): self {
        $this->limitValue = $limit;
        return $this;
    }
    
    public function offset(int $offset): self {
        $this->offsetValue = $offset;
        return $this;
    }
    
    public function find(int $id): ?array {
        $this->where('id', '=', $id)->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }
    
    public function first(): ?array {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }
    
    public function get(): array {
        $sql = $this->buildSelectQuery();
        $stmt = $this->connection->getPdo()->prepare($sql);
        $stmt->execute($this->bindings);
        
        $results = $stmt->fetchAll();
        $this->reset();
        
        return $results;
    }
    
    public function count(): int {
        $originalColumns = $this->columns;
        $this->columns = ['COUNT(*) as count'];
        
        $sql = $this->buildSelectQuery();
        $stmt = $this->connection->getPdo()->prepare($sql);
        $stmt->execute($this->bindings);
        
        $result = $stmt->fetch();
        $this->columns = $originalColumns;
        $this->reset();
        
        return (int) ($result['count'] ?? 0);
    }
    
    public function insert(array $data): int {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":$col", $columns);
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        
        $stmt = $this->connection->getPdo()->prepare($sql);
        $stmt->execute($data);
        
        $id = $this->connection->getPdo()->lastInsertId();
        $this->reset();
        
        return (int) $id;
    }
    
    public function update(array $data): bool {
        $sets = [];
        foreach ($data as $column => $value) {
            $sets[] = "$column = :$column";
            $this->bindings[$column] = $value;
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets);
        
        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . $this->buildWhereClause();
        }
        
        $stmt = $this->connection->getPdo()->prepare($sql);
        $result = $stmt->execute($this->bindings);
        
        $this->reset();
        return $result;
    }
    
    public function delete(): bool {
        $sql = "DELETE FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . $this->buildWhereClause();
        }
        
        $stmt = $this->connection->getPdo()->prepare($sql);
        $result = $stmt->execute($this->bindings);
        
        $this->reset();
        return $result;
    }
    
    protected function buildSelectQuery(): string {
        $sql = 'SELECT ' . implode(', ', $this->columns);
        $sql .= " FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . $this->buildWhereClause();
        }
        
        if (!empty($this->orderBys)) {
            $orderClauses = array_map(
                fn($order) => "{$order['column']} {$order['direction']}",
                $this->orderBys
            );
            $sql .= ' ORDER BY ' . implode(', ', $orderClauses);
        }
        
        if ($this->limitValue !== null) {
            $sql .= " LIMIT {$this->limitValue}";
        }
        
        if ($this->offsetValue !== null) {
            $sql .= " OFFSET {$this->offsetValue}";
        }
        
        return $sql;
    }
    
    protected function buildWhereClause(): string {
        $clauses = [];
        $bindingIndex = 0;
        
        foreach ($this->wheres as $index => $where) {
            $placeholder = "where_$bindingIndex";
            $this->bindings[$placeholder] = $where['value'];
            
            $clause = "{$where['column']} {$where['operator']} :$placeholder";
            
            if ($index === 0) {
                $clauses[] = $clause;
            } else {
                $clauses[] = "{$where['type']} $clause";
            }
            
            $bindingIndex++;
        }
        
        return implode(' ', $clauses);
    }
    
    protected function reset(): void {
        $this->columns = ['*'];
        $this->wheres = [];
        $this->orderBys = [];
        $this->limitValue = null;
        $this->offsetValue = null;
        $this->bindings = [];
    }
}