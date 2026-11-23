<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::apiResource('clients', ClientController::class)->names([
    'index'   => 'api.clients.index',
    'store'   => 'api.clients.store',
    'show'    => 'api.clients.show',
    'update'  => 'api.clients.update',
    'destroy' => 'api.clients.destroy',
]);
