<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ResumeController;
use App\Http\Controllers\Api\TranscriptionController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('client')->group(function () {
    Route::post('/auth/register', [ClientController::class, 'register']);

    Route::middleware('auth.token')->group(function () {
        Route::get('/auth/profile', [ClientController::class, 'profile']);

        Route::prefix('transcriptions')->group(function () {
            Route::get('/', [TranscriptionController::class, 'list']);
            Route::post('/', [TranscriptionController::class, 'convert']);
            Route::post('/generate-presigned', [TranscriptionController::class, 'generatePresignedUrl']);
            Route::get('/find', [TranscriptionController::class, 'find']);
            Route::delete('/', [TranscriptionController::class, 'delete']);
        });

        Route::prefix('resumes')->group(function () {
            Route::get('/', [ResumeController::class, 'list']);
            Route::post('/', [ResumeController::class, 'create']);
            Route::delete('/{id}', [ResumeController::class, 'delete']);
        });
    });

    Route::post('/webhooks/transcriptions', [WebhookController::class, 'watchTranscription']);
});
