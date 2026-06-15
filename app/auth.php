<?php

if (!auth()->check()) {
    redirect('/admin/login');
}

if ((time() - (int) session()->get('kn_last_activity', 0)) > 3600) {
    auth()->logout();
    redirect('/admin/login?expired=1');
}

session()->set('kn_last_activity', time());
