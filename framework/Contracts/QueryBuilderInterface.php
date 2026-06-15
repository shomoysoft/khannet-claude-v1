<?php
namespace Framework\Contracts;

interface QueryBuilderInterface {
    public function table(string $table): self;
    public function select(array $columns = ['*']): self;
    public function where(string $column, string $operator, $value = null): self;
    public function orWhere(string $column, string $operator, $value = null): self;
    public function orderBy(string $column, string $direction = 'ASC'): self;
    public function limit(int $limit): self;
    public function offset(int $offset): self;
    public function find(int $id): ?array;
    public function first(): ?array;
    public function get(): array;
    public function insert(array $data): int;
    public function update(array $data): bool;
    public function delete(): bool;
    public function count(): int;
}