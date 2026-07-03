<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('/provinces', [SettingController::class, 'provinces']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/documents/{document}/download', [DocumentController::class, 'download']);

    Route::apiResource('/projects', ProjectController::class);

    Route::get('profile', [AuthController::class, 'getProfile']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::controller(NotificationController::class)->group(function () {
        Route::get('/notifications',  'index');
        Route::get('/notifications/unread-count',  'unreadCount');
        Route::patch('/notifications/markAsRead',  'markAsRead');
    });

    Route::get('getClients/{role}', [CustomerController::class, 'getClients']);
    Route::get('getSteps/{project}', [ProjectController::class, 'getSteps']);

    Route::middleware('user-type:client')->prefix('client')->group(function () {

        Route::controller(ClientController::class)->group(function () {
            Route::get('getTotals',  'getTotals');
            Route::get('getNewProjects',  'getNewProjects');
            Route::get('getProjects/{status}',  'getProjects');
            Route::get('isActive',  'isActive');

            Route::post('addOffer/{project}',  'addOffer');
            Route::post('addStep/{project}',  'addStep');
            Route::post('end/{project}',  'endProject');
        });
    });
    Route::middleware('user-type:customer')->prefix('customer')->group(function () {
        Route::controller(ProjectController::class)->group(function () {
            Route::get('getCustomerProjects', 'getCustomerProjects');
        });
        Route::controller(CustomerController::class)->group(function () {
            Route::get('getOffers/{project}', 'getOffers');
            Route::post('acceptOffer/{project}/{offer}', 'acceptOffer');
            Route::post('rate/{project}', 'rate');
        });
    });
});

Route::fallback(function () {
    return apiError("path does not exist !!! 😁", [
        'url' => URL::current()
    ]);
});