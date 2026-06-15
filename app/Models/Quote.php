<?php

namespace App\Models;

use Framework\Database\DB;
use Framework\Database\Model;

class Quote extends Model
{
    protected $table = 'shomoysoft_quotes';

    public const STATUSES = ['new', 'contacted', 'completed', 'cancelled'];

    public static function create(array $data): int
    {
        return (int) DB::table(static::getTableName())->insert($data);
    }

    public static function filtered(string $status, string $search, int $page, int $per = 20): array
    {
        $table  = static::getTableName();
        $conds  = ['1=1'];
        $params = [];

        if ($status !== '') {
            $conds[]  = 'status = ?';
            $params[] = $status;
        }
        if ($search !== '') {
            $conds[]  = '(name LIKE ? OR mobile LIKE ? OR service LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $where  = implode(' AND ', $conds);
        $total  = (int)(DB::select("SELECT COUNT(*) AS c FROM {$table} WHERE {$where}", $params)[0]['c'] ?? 0);
        $pages  = max(1, (int) ceil($total / $per));
        $offset = ($page - 1) * $per;
        $rows   = DB::select(
            "SELECT * FROM {$table} WHERE {$where} ORDER BY created_at DESC LIMIT {$per} OFFSET {$offset}",
            $params
        );

        return compact('rows', 'total', 'pages');
    }

    public static function stats(): array
    {
        $table = static::getTableName();
        return DB::select("
            SELECT
              COUNT(*)                        AS total,
              SUM(status = 'new')             AS new,
              SUM(status = 'completed')       AS completed,
              SUM(DATE(created_at) = CURDATE()) AS today
            FROM {$table}
        ")[0] ?? [];
    }

    public static function recent(int $limit = 5): array
    {
        return DB::table(static::getTableName())->orderBy('created_at', 'DESC')->limit($limit)->get();
    }

    public static function allOrdered(): array
    {
        return DB::table(static::getTableName())->orderBy('created_at', 'DESC')->get();
    }
}
