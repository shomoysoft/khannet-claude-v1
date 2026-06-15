<?php

namespace App\Controllers;

use Framework\Http\Controller;
use Framework\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request): void
    {
        if (auth()->check()) {
            redirect('/admin');
        }

        if ($request->isPost()) {
            if (!csrf()->verify($request->input('csrf_token', ''))) {
                flash('error', 'Invalid security token. Please try again.');
            } elseif (auth()->attempt([
                'username' => $request->input('username', ''),
                'password' => $request->input('password', ''),
            ])) {
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
        auth()->logout();
        redirect('/admin/login');
    }
}
