<?php

namespace App\Controllers;

use App\Models\ConnectionRequest;
use App\Models\Quote;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $cr        = ConnectionRequest::stats();
        $sq        = Quote::stats();
        $recent_cr = ConnectionRequest::recent();
        $recent_sq = Quote::recent();

        $this->view('dashboard', compact('cr', 'sq', 'recent_cr', 'recent_sq'));
    }
}
