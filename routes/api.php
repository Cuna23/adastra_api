<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AssetCategoryController;
use App\Http\Controllers\API\AssetController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\IncidentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [AuthController::class, 'login']);

// Protected
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('asset-categories', AssetCategoryController::class);
    Route::apiResource('assets', AssetController::class);
    Route::apiResource('departments', DepartmentController::class)->only(['index']);
    Route::put('incidents/{incident}/logs/{log}', [IncidentController::class, 'updateLog']);
    Route::delete('incidents/{incident}/logs/{log}', [IncidentController::class, 'destroyLog']);
    Route::get('incidents/stats/chart', [IncidentController::class, 'chartStats']);  
    Route::apiResource('incidents', IncidentController::class);
}); 