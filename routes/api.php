<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;

use App\Http\Controllers\Project\StepController;
use App\Http\Controllers\ProviderController;
// use App\Http\Controllers\DocumentController;
// use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Project\OfferController;
use App\Http\Controllers\Project\ProjectInvitationController;
use App\Http\Controllers\Project\ProjectReportController;
use App\Http\Controllers\Interaction\RatingController;
use App\Http\Controllers\Interaction\ComplaintController;
use App\Http\Controllers\Interaction\FavoriteController;
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
 Route::post('change-password', [AuthController::class, 'changePassword']);
 Route::get('favorites', [FavoriteController::class, 'index']);
 Route::post('favorites/toggle', [FavoriteController::class, 'toggle']);
 Route::delete('favorites/{id}', [FavoriteController::class, 'destroy']);

//     Route::controller(NotificationController::class)->group(function () {
//         Route::get('/notifications',  'index');
//         Route::get('/notifications/unread-count',  'unreadCount');
//         Route::patch('/notifications/markAsRead',  'markAsRead');
   });
   Route::middleware('auth:sanctum')->group(function(){

    Route::get('/projects',[ProjectController::class,'index']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);
    Route::post('/projects', [ProjectController::class, 'store']);
  Route::put('/projects/{project}', [ProjectController::class, 'update']);
  Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);

});


Route::prefix('admin')
    ->middleware(['auth:sanctum', 'user.type:admin'])
    ->group(function () {

        Route::get('ratings', [RatingController::class, 'index']);
        Route::get('ratings/{rating}', [RatingController::class, 'adminShow']);
        Route::delete('ratings/{rating}', [RatingController::class, 'adminDestroy']);

    });

//     Route::get('getClients/{role}', [CustomerController::class, 'getClients']);
//     Route::get('getSteps/{project}', [ProjectController::class, 'getSteps']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/provider/dashboard', [ProviderController::class, 'dashboard']);
    Route::get('/provider/public-tenders', [ProviderController::class, 'publicTenders']);
    Route::get('/provider/private-tenders', [ProviderController::class, 'privateTenders']);
    Route::get('/provider/tenders/{id}', [ProviderController::class, 'showTender']);
    Route::post('/provider/invitations/{id}/decline',[ProviderController::class, 'declineInvitation']);

    Route::get('/provider/offers',[OfferController::class, 'myOffers']);
    Route::post('/provider/projects/{project}/offers', [OfferController::class, 'store']);
    Route::put('/provider/offers/{offer}', [OfferController::class, 'update']);
    Route::delete('/provider/offers/{offer}', [OfferController::class, 'destroy']);

    Route::get('/provider/projects', [ProviderController::class, 'projects']);
    Route::get('/provider/projects/{project}', [ProviderController::class, 'showProject']);
    Route::get('/provider/projects/{project}/tracking', [ProviderController::class, 'projectTracking']);
    Route::post('/provider/projects/{project}/reports', [ProviderController::class, 'addReport']);
    Route::post('/provider/projects/{project}/end', [ProviderController::class, 'endProject']);
    Route::get('/provider/projects/{project}/steps', [StepController::class, 'index']);
    Route::post('/provider/projects/{project}/steps', [StepController::class, 'store']);
    Route::get('/provider/projects/{project}/steps/{step}', [StepController::class, 'show']);
    Route::put('/provider/steps/{step}', [StepController::class, 'update']);
    Route::delete('/provider/steps/{step}', [StepController::class, 'destroy']);
    Route::get('/provider/complaints', [ComplaintController::class, 'myComplaints']);
    Route::post('/provider/complaints', [ComplaintController::class, 'store']);
    Route::get('/provider/complaints/{complaint}', [ComplaintController::class, 'show']);
    Route::get('/provider/complaints-against-me', [ComplaintController::class, 'complaintsAgainstMe']);

  
    
   Route::get('/provider/invitations',[ProjectInvitationController::class, 'index']);
   Route::get('/provider/invitations/{invitation}',[ProjectInvitationController::class, 'show']);
   Route::post('/provider/invitations/{invitation}/accept',[ProjectInvitationController::class, 'accept']);
   Route::post('/provider/invitations/{invitation}/decline',[ProjectInvitationController::class, 'decline']);

   Route::post('/provider/projects/{project}/reports',[ProjectReportController::class, 'store']);
   Route::get('/provider/projects/{project}/reports', [ProjectReportController::class, 'index']);
   Route::get('/provider/projects/{project}/reports/{report}',[ProjectReportController::class, 'show']);
   Route::put('provider/reports/{report}',[ProjectReportController::class, 'update']);
   Route::delete('provider/reports/{report}', [ProjectReportController::class, 'destroy']);
   
    Route::get('provider/ratings',[RatingController::class, 'providerRatings']);
   Route::get('provider/ratings/{rating}',[RatingController::class, 'providerShow']);



});

    Route::middleware(['auth:sanctum', 'user.type:client'])->prefix('client')->group(function () {
    Route::get('projects',[ClientController::class, 'projects']);
    Route::get('projects/{project}',[ClientController::class, 'show']);
    Route::get('projects/{project}/offers',[ClientController::class, 'getOffers']);
    Route::post('projects/{project}/offers/{offer}/accept',[OfferController::class, 'acceptOffer']);
    Route::post('projects/{project}/offers/{offer}/reject',[OfferController::class, 'rejectOffer']);
    Route::post('projects/{project}/rate', [ClientController::class, 'rate']);
    Route::get('projects/{project}/steps', [StepController::class, 'clientIndex']);
    Route::get('projects/{project}/steps/{step}', [StepController::class, 'clientShow']);
    Route::get('complaints', [ComplaintController::class, 'myComplaints']);
    Route::post('complaints', [ComplaintController::class, 'store']);
    Route::get('complaints/{complaint}', [ComplaintController::class, 'show']);
      Route::get('/complaints-against-me', [ComplaintController::class, 'complaintsAgainstMe']);
    Route::get('projects/{project}/steps', [StepController::class, 'clientIndex']);
    Route::get('projects/{project}/steps/{step}', [StepController::class, 'clientShow']);


    Route::post('projects/{project}/invitations',[ProjectInvitationController::class, 'store']);
    Route::delete('invitations/{invitation}',[ProjectInvitationController::class, 'destroy']);
    
    Route::get( 'projects/{project}/reports',[ProjectReportController::class, 'clientIndex'] );
    Route::get('projects/{project}/reports/{report}',[ProjectReportController::class, 'clientShow']);

    Route::post('projects/{project}/ratings', [RatingController::class, 'store']);
    Route::put('ratings/{rating}',[RatingController::class, 'update']);
    Route::delete('ratings/{rating}',[RatingController::class, 'destroy']);
    Route::get('ratings/{rating}',[RatingController::class, 'show']);
    Route::get('my-ratings',[RatingController::class, 'myRatings']);
 
          });

Route::middleware(['auth:sanctum', 'user.type:admin'])->prefix('admin')->group(function () {
    Route::get('/steps', [StepController::class, 'adminIndex']);
    Route::get('/steps/{step}', [StepController::class, 'adminShow']);
    Route::delete('/steps/{step}', [StepController::class, 'adminDestroy']);
    Route::get('/complaints', [ComplaintController::class, 'index']);
    Route::post('/complaints/{complaint}/action', [ComplaintController::class, 'takeAction']);
});
//     });
// });

// Route::fallback(function () {
//     return apiError("path does not exist !!! 😁", [
//         'url' => URL::current()
//     ]);
// });