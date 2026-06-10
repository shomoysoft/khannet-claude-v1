<?php

namespace KhanNet\Controllers;

use App\Database\DB;

class QuoteController extends BaseController
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
            $conds[]  = '(name LIKE ? OR mobile LIKE ? OR service LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $where  = implode(' AND ', $conds);
        $total  = (int)(DB::select("SELECT COUNT(*) AS c FROM shomoysoft_quotes WHERE {$where}", $params)[0]['c'] ?? 0);
        $pages  = max(1, (int)ceil($total / $per));
        $offset = ($page - 1) * $per;
        $rows   = DB::select("SELECT * FROM shomoysoft_quotes WHERE {$where} ORDER BY created_at DESC LIMIT {$per} OFFSET {$offset}", $params);

        $this->view('quotes', compact('rows', 'total', 'pages', 'page', 'status', 'search'));
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
        $allowed = ['new', 'contacted', 'completed', 'cancelled'];

        if (!$id || !in_array($status, $allowed, true)) {
            redirect('quotes.php');
        }

        DB::table('shomoysoft_quotes')->where('id', $id)->update([
            'status' => $status,
            'notes'  => $notes,
        ]);

        redirect('quotes.php?saved=1');
    }

    public function export(): void
    {
        $this->requireAuth();

        $rows   = DB::table('shomoysoft_quotes')->orderBy('created_at', 'DESC')->get();
        $file   = 'shomoysoft-quotes-' . date('Y-m-d') . '.csv';
        $fields = ['id', 'name', 'mobile', 'service', 'budget', 'details', 'status', 'notes', 'created_at'];

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$file}\"");

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['ID', 'Name', 'Mobile', 'Service', 'Budget', 'Details', 'Status', 'Notes', 'Received']);
        foreach ($rows as $row) {
            fputcsv($out, array_map(fn($f) => $row[$f] ?? '', $fields));
        }
        fclose($out);
        exit;
    }
}
