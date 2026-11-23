<?php

use App\Http\Controllers\ClientWebController;
use App\Http\Controllers\CompteWebController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VirementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('clients', ClientWebController::class);

Route::resource('comptes', CompteWebController::class);

Route::resource('virements', VirementController::class)->only(['index', 'create', 'store']);

