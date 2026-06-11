<?php

namespace KhanNet\Controllers;

use App\Database\DB;
use App\Http\Request;
use KhanNet\Models\ConnectionRequest;

class ConnectionController extends BaseController
{
    public function index(Request $request): void
    {
        $status = $request->input('status', '');
        $search = $request->input('search', '');
        $page   = max(1, (int) $request->input('page', 1));

        $result = ConnectionRequest::filtered($status, $search, $page);

        $this->view('connections', array_merge($result, compact('status', 'search', 'page')));
    }

    public function updateStatus(Request $request): void
    {
        if (!$request->isPost() || !csrf()->verify($request->input('csrf_token', ''))) {
            abort(403, 'Forbidden');
        }

        if (!$request->validate([
            'id'     => 'required|integer',
            'status' => 'required|in:new,contacted,connected,cancelled',
            'notes'  => 'nullable',
        ])) {
            redirect('/admin/connections');
        }

        $data = $request->validated();

        DB::table('connection_requests')->where('id', (int) $data['id'])->update([
            'status' => $data['status'],
            'notes'  => $data['notes'] ?? '',
        ]);

        redirect('/admin/connections?saved=1');
    }

    public function export(): void
    {
        $rows   = ConnectionRequest::allOrdered();
        $file   = 'khannet-connections-' . date('Y-m-d') . '.csv';
        $fields = ['id', 'name', 'mobile', 'area', 'plan', 'address', 'message', 'status', 'notes', 'created_at'];

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$file}\"");

        $out = fopen('php://output', 'w');
        fprintf($out, \chr(0xEF) . \chr(0xBB) . \chr(0xBF));
        fputcsv($out, ['ID', 'Name', 'Mobile', 'Area', 'Plan', 'Address', 'Message', 'Status', 'Notes', 'Received']);
        foreach ($rows as $row) {
            fputcsv($out, array_map(fn($f) => $row[$f] ?? '', $fields));
        }
        fclose($out);
        exit;
    }
}
