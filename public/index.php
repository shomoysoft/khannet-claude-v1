<?php

require_once __DIR__ . '/../app/bootstrap.php';

require ROOT . '/routes/web.php';
require ROOT . '/routes/api.php';

Framework\Routing\Route::dispatch();
