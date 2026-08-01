<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ClientController;
// use App\Http\Controllers\DocumentController;
// use App\Http\Controllers\NotificationController;
// use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\URL;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/provinces', [SettingController::class, 'provinces']);

 Route::middleware('auth:sanctum')->group(function () {

//     Route::get('/documents/{document}/download', [DocumentController::class, 'download']);

//     Route::apiResource('/projects', ProjectController::class);

 Route::get('profile', [AuthController::class, 'getProfile']);
 Route::put('profile/update', [AuthController::class, 'updateProfile']);
 Route::post('logout', [AuthController::class, 'logout']);

//     Route::controller(NotificationController::class)->group(function () {
//         Route::get('/notifications',  'index');
//         Route::get('/notifications/unread-count',  'unreadCount');
//         Route::patch('/notifications/markAsRead',  'markAsRead');
   });

//     Route::get('getClients/{role}', [CustomerController::class, 'getClients']);
//     Route::get('getSteps/{project}', [ProjectController::class, 'getSteps']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/provider/dashboard', [ProviderController::class, 'dashboard']);
    Route::get('/provider/public-tenders', [ProviderController::class, 'publicTenders']);
    Route::get('/provider/private-tenders', [ProviderController::class, 'privateTenders']);
    Route::get('/provider/tenders/{id}', [ProviderController::class, 'showTender']);
    Route::post('/provider/invitations/{id}/decline',[ProviderController::class, 'declineInvitation']);
    Route::get('/provider/offers',[ProviderController::class, 'myOffers']);
    Route::post('/provider/projects/{project}/offers', [ProviderController::class, 'storeOffer']);
    Route::put('/provider/offers/{offer}', [ProviderController::class, 'updateOffer']);
    Route::delete('/provider/offers/{offer}', [ProviderController::class, 'deleteOffer']);
    Route::get('/provider/projects', [ProviderController::class, 'projects']);
    Route::get('/provider/projects/{project}', [ProviderController::class, 'showProject']);
    Route::get('/provider/projects/{project}/tracking', [ProviderController::class, 'projectTracking']);
    Route::post('/provider/projects/{project}/reports', [ProviderController::class, 'addReport']);
    Route::post('/provider/projects/{project}/end', [ProviderController::class, 'endProject']);

});

    Route::middleware(['auth:sanctum', 'user.type:client'])->prefix('client')->group(function () {
    Route::get('projects',[ClientController::class, 'projects']);
    Route::get('projects/{project}',[ClientController::class, 'show']);
    Route::get('projects/{project}/offers',[ClientController::class, 'getOffers']);
    Route::post('projects/{project}/offers/{offer}/accept',[ClientController::class, 'acceptOffer']);
    Route::post('projects/{project}/offers/{offer}/reject',[ClientController::class, 'rejectOffer']);
    Route::post('projects/{project}/rate', [ClientController::class, 'rate']);
    Route::get('complaints', [ClientController::class, 'complaints']);
    Route::post('complaints',[ClientController::class, 'storeComplaint']);

          });
//     });
// });

// Route::fallback(function () {
//     return apiError("path does not exist !!! 😁", [
//         'url' => URL::current()
//     ]);
// });