<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/profile', [AuthController::class, 'profile']);

        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'list']);
            Route::post('/', [UserController::class, 'create']);
        });

        Route::prefix('clients')->group(function () {
            Route::get('/', [ClientController::class, 'list']);
            Route::delete('/{id}', [ClientController::class, 'delete']);
        });

        Route::prefix('webhooks')->group(function () {
            Route::get('/', [WebhookController::class, 'list']);
            Route::post('/', [WebhookController::class, 'create']);
            Route::delete('/', [WebhookController::class, 'deleteByExternal']);
        });
    });
});
