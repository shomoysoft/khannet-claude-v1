<?php
namespace App\Contracts;

use PDO;

interface ConnectionInterface {
    public function getPdo(): PDO;
}