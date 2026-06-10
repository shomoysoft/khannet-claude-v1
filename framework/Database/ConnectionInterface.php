<?php
namespace App\Database;

use PDO;

interface ConnectionInterface {
    public function getPdo(): PDO;
}