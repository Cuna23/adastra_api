<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AssetCategoryController;
use App\Http\Controllers\API\AssetController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\IncidentController;
use App\Http\Controllers\API\ServiceRequestController; 
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\CalendarController;
use App\Http\Controllers\API\ReminderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MicrosoftAuthController;

// Public
Route::post('/login', [AuthController::class, 'login']);
    // Microsoft OAuth
Route::get('/auth/microsoft/redirect', [MicrosoftAuthController::class, 'redirect']);
Route::get('/auth/microsoft/callback', [MicrosoftAuthController::class, 'callback']);

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
    //company management
    Route::get('/org-chart', [CompanyController::class, 'orgChart']);
    Route::post('/org-chart', [CompanyController::class, 'storeOrgChart']);
    Route::get('/floor-maps', [CompanyController::class, 'floorMaps']);
    Route::post('/floor-maps', [CompanyController::class, 'storeFloorMap']);
    Route::delete('/company/{id}', [CompanyController::class, 'destroy']);
    Route::get('/about', [CompanyController::class, 'about']);
    Route::get('/vision-mission', [CompanyController::class, 'visionMission']);
    Route::post('/company/content', [CompanyController::class, 'upsertContent']);
    Route::patch('/company/{id}/title', [CompanyController::class, 'updateTitle']);
    //dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'index']);
    //calendar & reminders
    Route::get('/calendar', [CalendarController::class, 'index']);
    Route::post('/reminders', [ReminderController::class, 'store']);
    Route::put('/reminders/{id}', [ReminderController::class, 'update']);
    Route::delete('/reminders/{id}', [ReminderController::class, 'destroy']);
    }); 