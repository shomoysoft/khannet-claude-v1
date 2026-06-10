<?php
require_once __DIR__ . '/../../app/bootstrap.php';
$type = input('type', '');
if ($type === 'connections') {
    (new KhanNet\Controllers\ConnectionController)->export();
} else {
    (new KhanNet\Controllers\QuoteController)->export();
}
