<?php

namespace KhanNet\Controllers\Api;

use App\Http\Controller;

abstract class ApiController extends Controller
{
    public function __construct()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
    }
}
