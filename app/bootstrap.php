<?php

require_once __DIR__ . '/../vendor/autoload.php';

define('APP',  __DIR__);
define('ROOT', dirname(__DIR__));

use App\Container\Container;
use App\Contracts\ConnectionInterface;
use App\Contracts\SessionInterface;
use App\Database\MySQLConnection;
use App\Database\ConnectionManager;
use App\Session\FileSession;
use App\Security\Csrf;

$dbCfg  = require ROOT . '/config/database.php';
$sesCfg = require ROOT . '/config/session.php';
$appCfg = require ROOT . '/config/app.php';

date_default_timezone_set($appCfg['timezone']);

define('ADMIN_USER', $appCfg['admin_user']);
define('ADMIN_PASS', $appCfg['admin_pass']);

Container::singleton(ConnectionInterface::class, fn() => new MySQLConnection($dbCfg));
ConnectionManager::setConnection(Container::make(ConnectionInterface::class));

Container::singleton(SessionInterface::class, fn() => new FileSession($sesCfg));
Container::singleton(Csrf::class, fn($c) => new Csrf($c->make(SessionInterface::class)));

Container::make(SessionInterface::class)->start();

require_once APP . '/helpers.php';
