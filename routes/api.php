<?php

use Framework\Routing\Route;
use App\Controllers\Api\ConnectionApiController;
use App\Controllers\Api\QuoteApiController;

Route::post('/api/submit', [ConnectionApiController::class, 'submit']);
Route::post('/api/quote',  [QuoteApiController::class,      'submit']);
