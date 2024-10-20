<?php


/**
 * Web routes are defined here.
 */

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UnblockIPController;

$router->get('/', [DashboardController::class, 'invoke']);
$router->post('/blacklisted/unblock/:id', [UnblockIPController::class, 'invoke']);