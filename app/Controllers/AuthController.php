<?php

namespace KhanNet\Controllers;

class AuthController extends BaseController
{
    public function login(): void
    {
        if (session()->get('kn_admin_logged_in')) {
            redirect('index.php');
        }

        if (is_post()) {
            if (!csrf()->verify(input('csrf_token', ''))) {
                flash('error', 'Invalid security token. Please try again.');
            } elseif (
                hash_equals(ADMIN_USER, (string) input('username', '')) &&
                hash_equals(ADMIN_PASS, (string) input('password', ''))
            ) {
                session()->regenerate(true);
                session()->set('kn_admin_logged_in', true);
                session()->set('kn_last_activity', time());
                redirect('index.php');
            } else {
                flash('error', 'Incorrect username or password.');
            }
        }

        require_once APP . '/Views/login.php';
    }

    public function logout(): void
    {
        session()->destroy();
        redirect('login.php');
    }
}
