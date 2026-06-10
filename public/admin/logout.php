<?php
require_once __DIR__ . '/../../app/bootstrap.php';
(new KhanNet\Controllers\AuthController)->logout();
