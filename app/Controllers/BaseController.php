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
        extract($data);
        require APP . '/Views/layout.php';
        require APP . '/Views/' . $name . '.php';
    }

    protected function requireRole(string ...$roles): void
    {
        $role = session()->get('user_role', '');
        if (!in_array($role, $roles, true)) {
            http_response_code(403);
            echo '<!DOCTYPE html><html><head><title>403 Forbidden</title>'
               . '<link rel="stylesheet" href="/assets/css/admin.css"></head><body>'
               . '<div style="display:flex;align-items:center;justify-content:center;min-height:100vh;flex-direction:column;gap:1rem;font-family:system-ui">'
               . '<h1 style="font-size:2rem;font-weight:800">403 — Forbidden</h1>'
               . '<p style="color:#64748b">You do not have permission to access this page.</p>'
               . '<a href="/admin" style="color:#0077b6">← Back to dashboard</a>'
               . '</div></body></html>';
            exit;
        }
    }
}
