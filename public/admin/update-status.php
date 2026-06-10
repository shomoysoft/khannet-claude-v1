<?php
require_once __DIR__ . '/../../app/bootstrap.php';
$type = input('type', '');
if ($type === 'connection') {
    (new KhanNet\Controllers\ConnectionController)->updateStatus();
} else {
    (new KhanNet\Controllers\QuoteController)->updateStatus();
}
