<?php

namespace App\Models;

use Framework\Database\DB;
use Framework\Database\Model;

class User extends Model
{
    protected $table = 'users';

    public const ROLES = ['super_admin', 'admin', 'viewer'];

    public const ROLE_LABELS = [
        'super_admin' => 'Super Admin',
        'admin'       => 'Admin',
        'viewer'      => 'Viewer',
    ];

    public static function findByUsername(string $username): ?static
    {
        return static::first('username', $username);
    }

    public static function allOrdered(): array
    {
        $rows = DB::table(static::getTableName())
            ->orderBy('created_at', 'ASC')
            ->get();
        return array_map(fn($row) => new static($row), $rows);
    }

    public static function create(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return (int) DB::table(static::getTableName())->insert($data);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin'], true);
    }

    public function can(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function roleLabel(): string
    {
        return self::ROLE_LABELS[$this->role] ?? $this->role;
    }
}
