<?php

use Framework\Routing\Route;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ConnectionController;
use App\Controllers\QuoteController;

Route::get('/admin',                    [DashboardController::class,  'index']);

Route::get('/admin/login',              [AuthController::class,       'login']);
Route::post('/admin/login',             [AuthController::class,       'login']);
Route::get('/admin/logout',             [AuthController::class,       'logout']);

Route::get('/admin/connections',        [ConnectionController::class, 'index']);
Route::post('/admin/connections',       [ConnectionController::class, 'updateStatus']);
Route::get('/admin/connections/export', [ConnectionController::class, 'export']);

Route::get('/admin/quotes',             [QuoteController::class,      'index']);
Route::post('/admin/quotes',            [QuoteController::class,      'updateStatus']);
Route::get('/admin/quotes/export',      [QuoteController::class,      'export']);
