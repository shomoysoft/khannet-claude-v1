<?php

namespace KhanNet\Controllers;

use App\Database\DB;

class ConnectionController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();

        $status = input('status', '');
        $search = trim(input('search', ''));
        $page   = max(1, (int) input('page', 1));
        $per    = 20;

        $conds  = ['1=1'];
        $params = [];

        if ($status !== '') {
            $conds[]  = 'status = ?';
            $params[] = $status;
        }
        if ($search !== '') {
            $conds[]  = '(name LIKE ? OR mobile LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $where  = implode(' AND ', $conds);
        $total  = (int)(DB::select("SELECT COUNT(*) AS c FROM connection_requests WHERE {$where}", $params)[0]['c'] ?? 0);
        $pages  = max(1, (int)ceil($total / $per));
        $offset = ($page - 1) * $per;
        $rows   = DB::select("SELECT * FROM connection_requests WHERE {$where} ORDER BY created_at DESC LIMIT {$per} OFFSET {$offset}", $params);

        $this->view('connections', compact('rows', 'total', 'pages', 'page', 'status', 'search'));
    }

    public function updateStatus(): void
    {
        $this->requireAuth();

        if (!is_post() || !csrf()->verify(input('csrf_token', ''))) {
            abort(403, 'Forbidden');
        }

        $id     = (int) input('id', 0);
        $status = input('status', '');
        $notes  = trim(input('notes', ''));
        $allowed = ['new', 'contacted', 'connected', 'cancelled'];

        if (!$id || !in_array($status, $allowed, true)) {
            redirect('connections.php');
        }

        DB::table('connection_requests')->where('id', $id)->update([
            'status' => $status,
            'notes'  => $notes,
        ]);

        redirect('connections.php?saved=1');
    }

    public function export(): void
    {
        $this->requireAuth();

        $rows   = DB::table('connection_requests')->orderBy('created_at', 'DESC')->get();
        $file   = 'khannet-connections-' . date('Y-m-d') . '.csv';
        $fields = ['id', 'name', 'mobile', 'area', 'plan', 'address', 'message', 'status', 'notes', 'created_at'];

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$file}\"");

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['ID', 'Name', 'Mobile', 'Area', 'Plan', 'Address', 'Message', 'Status', 'Notes', 'Received']);
        foreach ($rows as $row) {
            fputcsv($out, array_map(fn($f) => $row[$f] ?? '', $fields));
        }
        fclose($out);
        exit;
    }
}
