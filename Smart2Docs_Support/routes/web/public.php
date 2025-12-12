<?php

use App\Http\Controllers\Public\ClientController;
use App\Http\Controllers\Public\DashboardController;
use App\Http\Controllers\Public\ProfileController;
use App\Http\Controllers\Public\SelectedTenantController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::prefix('perfil')
        ->name('profile.')
        ->controller(ProfileController::class)
        ->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::patch('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
        });

    Route::get('/configuracoes', [SelectedTenantController::class, 'settings'])
        ->name('tenant.settings');

    Route::prefix('clientes')
        ->name('clients.')
        ->controller(ClientController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
        });
});
