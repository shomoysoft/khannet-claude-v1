<?php
namespace Framework\Contracts;

use PDO;

interface ConnectionInterface {
    public function getPdo(): PDO;
}