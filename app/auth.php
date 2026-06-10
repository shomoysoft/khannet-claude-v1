<?php

require_once APP . '/bootstrap.php';

if (!session()->get('kn_admin_logged_in')) {
    redirect('login.php');
}

if ((time() - (int) session()->get('kn_last_activity', 0)) > 3600) {
    session()->destroy();
    redirect('login.php?expired=1');
}

session()->set('kn_last_activity', time());
