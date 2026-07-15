<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AssetCategoryController;
use App\Http\Controllers\API\AssetController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\IncidentController;
use App\Http\Controllers\API\ServiceRequestController; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [AuthController::class, 'login']);

// Protected
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    //user management
    Route::apiResource('users', UserController::class);
        Route::apiResource('departments', DepartmentController::class)->only(['index']);
    //asset management
    Route::apiResource('asset-categories', AssetCategoryController::class);
    Route::apiResource('assets', AssetController::class);
    //incident management
    Route::put('incidents/{incident}/logs/{log}', [IncidentController::class, 'updateLog']);
    Route::delete('incidents/{incident}/logs/{log}', [IncidentController::class, 'destroyLog']);
    Route::get('incidents/stats/chart', [IncidentController::class, 'chartStats']);  
    Route::get('/incidents/department-stats', [IncidentController::class, 'departmentStats']);
    Route::apiResource('incidents', IncidentController::class);
    //service request management
    Route::apiResource('service-requests', ServiceRequestController::class);
    Route::patch('service-requests/{service_request}/approve', [ServiceRequestController::class, 'approve']);
    Route::patch('service-requests/{service_request}/reject', [ServiceRequestController::class, 'reject']);
    Route::put('service-requests/{service_request}/note', [ServiceRequestController::class, 'addNote']);
    Route::patch('service-requests/{service_request}/edit-approval', [ServiceRequestController::class, 'editApproval']);
}); 