<?php

require_once __DIR__ . '/../vendor/autoload.php';

define('APP',  __DIR__);
define('ROOT', dirname(__DIR__));

use Framework\Container\Container;
use Framework\Contracts\AuthInterface;
use Framework\Contracts\ConnectionInterface;
use Framework\Contracts\SessionInterface;
use Framework\Auth\SessionAuth;
use Framework\Database\Connections\MySQLConnection;
use Framework\Database\ConnectionManager;
use Framework\Session\FileSession;
use Framework\Security\Csrf;
use Framework\Support\Logger;

$dbCfg  = require ROOT . '/config/database.php';
$sesCfg = require ROOT . '/config/session.php';
$appCfg = require ROOT . '/config/app.php';

date_default_timezone_set($appCfg['timezone']);

Logger::init();

define('ADMIN_USER', $appCfg['admin_user']);
define('ADMIN_PASS', $appCfg['admin_pass']);

Container::singleton(ConnectionInterface::class, fn() => new MySQLConnection($dbCfg));
ConnectionManager::setConnection(Container::make(ConnectionInterface::class));

Container::singleton(SessionInterface::class, fn() => new FileSession($sesCfg));
Container::singleton(AuthInterface::class,    fn($c) => new SessionAuth($c->make(SessionInterface::class)));
Container::singleton(Csrf::class,             fn($c) => new Csrf($c->make(SessionInterface::class)));

Container::make(SessionInterface::class)->start();

require_once APP . '/helpers.php';

