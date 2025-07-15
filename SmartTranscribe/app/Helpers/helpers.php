<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('currentClient')) {
    function currentClient()
    {
        return request()->attributes->get('client');
    }
}

if (!function_exists('insertLog')) {
    function insertLog($message, $e, $data = null)
    {
        Log::error($message, [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'row' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'data' => $data,
        ]);
    }
}
