<?php

use App\Http\Controllers\ResumeController;
use App\Http\Controllers\TranscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(['message' => 'Welcome to the API']));

Route::group(['prefix' => 'transcriptions'], function () {
    Route::get('/', [TranscriptionController::class, 'list']);
    Route::post('/', [TranscriptionController::class, 'convert']);
    Route::get('/find', [TranscriptionController::class, 'find']);
    Route::delete('/', [TranscriptionController::class, 'delete']);
});

Route::group(['prefix' => 'resumes'], function () {
    Route::get('/', [ResumeController::class, 'list']);
    Route::post('/', [ResumeController::class, 'create']);
    Route::delete('/{id}', [ResumeController::class, 'delete']);
});