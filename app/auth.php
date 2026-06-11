<?php

require_once APP . '/bootstrap.php';

if (!session()->get('kn_admin_logged_in')) {
    redirect('/admin/login');
}

if ((time() - (int) session()->get('kn_last_activity', 0)) > 3600) {
    session()->destroy();
    redirect('/admin/login?expired=1');
}

session()->set('kn_last_activity', time());
