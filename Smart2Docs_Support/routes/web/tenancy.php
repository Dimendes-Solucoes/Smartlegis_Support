<?php

use App\Http\Controllers\Tenancy\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant.connection'])
    ->group(function () {
        Route::prefix('usuarios')
            ->name('users.')
            ->controller(UserController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
            });
    });
