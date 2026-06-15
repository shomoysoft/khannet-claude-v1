<?php

namespace App\Controllers;

use Framework\Http\Controller;
use Framework\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request): void
    {
        if (session()->get('kn_admin_logged_in')) {
            redirect('/admin');
        }

        if ($request->isPost()) {
            if (!csrf()->verify($request->input('csrf_token', ''))) {
                flash('error', 'Invalid security token. Please try again.');
            } elseif (
                hash_equals(ADMIN_USER, (string) $request->input('username', '')) &&
                hash_equals(ADMIN_PASS, (string) $request->input('password', ''))
            ) {
                session()->regenerate(true);
                session()->set('kn_admin_logged_in', true);
                session()->set('kn_last_activity', time());
                redirect('/admin');
            } else {
                flash('error', 'Incorrect username or password.');
            }
        }

        require_once APP . '/Views/login.php';
    }

    public function logout(): void
    {
        session()->destroy();
        redirect('/admin/login');
    }
}
