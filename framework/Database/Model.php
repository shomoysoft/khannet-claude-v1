<?php
namespace Framework\Database;

use ReflectionClass;

abstract class Model {
    
    protected $table;
    protected $primaryKey = 'id';
    protected $attributes = [];
    
    public function __construct(array $attributes = []) {
        $this->fill($attributes);
    }
    
    public function __get($key) {
        return $this->attributes[$key] ?? null;
    }
    
    public function __set($key, $value) {
        $this->attributes[$key] = $value;
    }
    
    protected function fill(array $attributes): void {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }
    
    protected static function getTableName(): string {
        $instance = new static();
        
        if ($instance->table) {
            return $instance->table;
        }
        
        $className = (new ReflectionClass(static::class))->getShortName();
        return strtolower($className) . 's';
    }
    
    protected static function getPrimaryKey(): string {
        $instance = new static();
        return $instance->primaryKey;
    }
    
    protected static function query() {
        return ConnectionManager::table(static::getTableName());
    }
    
    public static function find($id) {
        $result = static::query()->find($id);
        return $result ? new static($result) : null;
    }
    
    public static function all(): array {
        $results = static::query()->get();
        return array_map(fn($row) => new static($row), $results);
    }
    
    public static function where($column, $operator, $value = null): array {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $results = static::query()->where($column, $operator, $value)->get();
        return array_map(fn($row) => new static($row), $results);
    }
    
    public static function first($column = null, $operator = null, $value = null) {
        if ($column === null) {
            $result = static::query()->first();
        } else {
            if ($value === null) {
                $value = $operator;
                $operator = '=';
            }
            $result = static::query()->where($column, $operator, $value)->first();
        }
        
        return $result ? new static($result) : null;
    }
    
    public static function count(): int {
        return static::query()->count();
    }
    
    public function save(): bool {
        $pk = $this->primaryKey;
        
        if (isset($this->attributes[$pk]) && $this->attributes[$pk]) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }
    
    protected function insert(): bool {
        $id = static::query()->insert($this->attributes);
        $this->attributes[$this->primaryKey] = $id;
        return true;
    }
    
    protected function update(): bool {
        $pk = $this->primaryKey;
        $id = $this->attributes[$pk];
        
        $data = $this->attributes;
        unset($data[$pk]);
        
        return static::query()->where($pk, '=', $id)->update($data);
    }
    
    public function delete(): bool {
        $pk = $this->primaryKey;
        $id = $this->attributes[$pk];
        
        return static::query()->where($pk, '=', $id)->delete();
    }
    
    public function toArray(): array {
        return $this->attributes;
    }
}