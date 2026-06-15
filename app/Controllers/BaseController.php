<?php

namespace App\Controllers;

use Framework\Http\Controller;

abstract class BaseController extends Controller
{
    public function __construct()
    {
        require_once APP . '/auth.php';
    }

    protected function view(string $name, array $data = []): void
    {
        require_once APP . '/Views/layout.php';
        extract($data);
        require APP . '/Views/' . $name . '.php';
    }
}
