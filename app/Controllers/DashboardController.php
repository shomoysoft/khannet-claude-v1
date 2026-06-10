<?php

namespace KhanNet\Controllers;

use App\Database\DB;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();

        $cr = DB::select("
            SELECT
              COUNT(*) AS total,
              SUM(status='new') AS new,
              SUM(status='connected') AS connected,
              SUM(DATE(created_at)=CURDATE()) AS today
            FROM connection_requests
        ")[0] ?? [];

        $sq = DB::select("
            SELECT
              COUNT(*) AS total,
              SUM(status='new') AS new,
              SUM(status='completed') AS completed,
              SUM(DATE(created_at)=CURDATE()) AS today
            FROM shomoysoft_quotes
        ")[0] ?? [];

        $recent_cr = DB::table('connection_requests')->orderBy('created_at', 'DESC')->limit(5)->get();
        $recent_sq = DB::table('shomoysoft_quotes')->orderBy('created_at', 'DESC')->limit(5)->get();

        $this->view('dashboard', compact('cr', 'sq', 'recent_cr', 'recent_sq'));
    }
}
