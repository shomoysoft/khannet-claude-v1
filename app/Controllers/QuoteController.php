<?php

namespace App\Controllers;

use Framework\Database\DB;
use Framework\Http\Request;
use App\Models\Quote;

class QuoteController extends BaseController
{
    public function index(Request $request): void
    {
        $status = $request->input('status', '');
        $search = $request->input('search', '');
        $page   = max(1, (int) $request->input('page', 1));

        $result = Quote::filtered($status, $search, $page);

        $this->view('quotes', array_merge($result, compact('status', 'search', 'page')));
    }

    public function updateStatus(Request $request): void
    {
        if (!$request->isPost() || !csrf()->verify($request->input('csrf_token', ''))) {
            abort(403, 'Forbidden');
        }

        if (!$request->validate([
            'id'     => 'required|integer',
            'status' => 'required|in:new,contacted,completed,cancelled',
            'notes'  => 'nullable',
        ])) {
            redirect('/admin/quotes');
        }

        $data = $request->validated();

        DB::table('shomoysoft_quotes')->where('id', (int) $data['id'])->update([
            'status' => $data['status'],
            'notes'  => $data['notes'] ?? '',
        ]);

        redirect('/admin/quotes?saved=1');
    }

    public function export(): void
    {
        $rows   = Quote::allOrdered();
        $file   = 'shomoysoft-quotes-' . date('Y-m-d') . '.csv';
        $fields = ['id', 'name', 'mobile', 'service', 'budget', 'details', 'status', 'notes', 'created_at'];

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$file}\"");

        $out = fopen('php://output', 'w');
        fprintf($out, \chr(0xEF) . \chr(0xBB) . \chr(0xBF));
        fputcsv($out, ['ID', 'Name', 'Mobile', 'Service', 'Budget', 'Details', 'Status', 'Notes', 'Received']);
        foreach ($rows as $row) {
            fputcsv($out, array_map(fn($f) => $row[$f] ?? '', $fields));
        }
        fclose($out);
        exit;
    }
}
