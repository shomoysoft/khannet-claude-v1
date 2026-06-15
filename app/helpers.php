<?php

use Framework\Database\DB;

function status_badge(string $status): string {
    $map = [
        'new'       => '<span class="badge badge-new">● New</span>',
        'contacted' => '<span class="badge badge-contacted">● Contacted</span>',
        'connected' => '<span class="badge badge-connected">● Connected</span>',
        'completed' => '<span class="badge badge-completed">● Completed</span>',
        'cancelled' => '<span class="badge badge-cancelled">● Cancelled</span>',
    ];
    return $map[$status] ?? '<span class="badge">' . e($status) . '</span>';
}

function time_ago(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff / 60) . 'm ago';
    if ($diff < 86400)  return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return date('d M Y', strtotime($datetime));
}

function new_count(string $table): int {
    try {
        return DB::table($table)->where('status', 'new')->count();
    } catch (Throwable) {
        return 0;
    }
}
