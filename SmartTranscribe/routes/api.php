<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => response()->json(['message' => 'Welcome to the API']));

require __DIR__ . '/api/admin.php';
require __DIR__ . '/api/client.php';
