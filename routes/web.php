<?php

use Illuminate\Support\Facades\Route;

Route::get('/storage/{path}', function (string $path) {
    \Log::info('STORAGE ROUTE HIT', ['path' => $path]); 
    
    $fullPath = storage_path('app/public/' . $path);
    $headers = ['Access-Control-Allow-Origin' => '*'];

    if (!file_exists($fullPath)) {
        return response('File not found', 404, $headers);
    }
    return response()->file($fullPath, $headers);
})->where('path', '.*');
