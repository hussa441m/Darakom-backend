<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;

use App\Http\Controllers\Project\StepController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Project\OfferController;
use App\Http\Controllers\Project\ProjectInvitationController;
use App\Http\Controllers\Project\ProjectReportController;
use App\Http\Controllers\Interaction\RatingController;
use App\Http\Controllers\Interaction\ComplaintController;
use App\Http\Controllers\Interaction\FavoriteController;
use App\Http\Controllers\SettingController;

use App\Http\Controllers\Service\ServiceCategoryController;
use App\Http\Controllers\Service\ArtisanServiceController;
use App\Http\Controllers\Admin\DocumentTypeController;

use App\Http\Controllers\Portfolio\PreviousWorkController;
use App\Http\Controllers\Portfolio\PreviousWorkImageController;
use App\Http\Controllers\Admin\ProjectTypeController;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/provinces', [SettingController::class, 'provinces']);

Route::get('portfolio/previous-works/{id}', [PreviousWorkController::class, 'show']);
Route::get('portfolio/profiles/{profileId}/previous-works', [PreviousWorkController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {



    Route::get('profile', [AuthController::class, 'getProfile']);
    Route::put('profile/update', [AuthController::class, 'updateProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
    Route::get('favorites', [FavoriteController::class, 'index']);
    Route::post('favorites/toggle', [FavoriteController::class, 'toggle']);
    Route::delete('favorites/{id}', [FavoriteController::class, 'destroy']);

    Route::get('document-types', [DocumentController::class, 'getTypes']);

    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index']);
        Route::post('/', [DocumentController::class, 'store']);
        Route::delete('/{id}', [DocumentController::class, 'destroy']);
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unread']);
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::patch('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });





    Route::prefix('portfolio')->group(function () {



        Route::get('previous-works', [PreviousWorkController::class, 'index']);
        Route::post('previous-works', [PreviousWorkController::class, 'store']);
        Route::put('previous-works/{id}', [PreviousWorkController::class, 'update']);
        Route::delete('previous-works/{id}', [PreviousWorkController::class, 'destroy']);





        Route::post('previous-works/{previousWorkId}/images', [PreviousWorkImageController::class, 'store']);
        Route::delete('images/{imageId}', [PreviousWorkImageController::class, 'destroy']);
        Route::patch('images/{imageId}/set-cover', [PreviousWorkImageController::class, 'setCover']);
    });





    Route::get('service-categories', [ServiceCategoryController::class, 'index']);
    Route::get('service-categories/{category}', [ArtisanServiceController::class, 'getProvidersByCategory']);
    Route::post('provider/service-category/toggle', [ArtisanServiceController::class, 'toggleProviderService']);








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

        Route::get('documents/providers/{profileId}', [DocumentController::class, 'getProviderDocuments']);
        Route::delete('documents/{id}', [DocumentController::class, 'adminDestroyDocument']);

        Route::get('document-types', [DocumentTypeController::class, 'index']);
        Route::post('document-types', [DocumentTypeController::class, 'store']);
        Route::put('document-types/{documentType}', [DocumentTypeController::class, 'update']);
        Route::delete('document-types/{documentType}', [DocumentTypeController::class, 'destroy']);






        Route::get('project-types', [ProjectTypeController::class, 'index']);
        Route::post('project-types', [ProjectTypeController::class, 'store']);
        Route::get('project-types/{projectType}', [ProjectTypeController::class, 'show']);
        Route::put('project-types/{projectType}', [ProjectTypeController::class, 'update']);
        Route::delete('project-types/{projectType}', [ProjectTypeController::class, 'destroy']);




        Route::get('roles', [RoleController::class, 'index']);
        Route::post('roles', [RoleController::class, 'store']);
        Route::get('roles/{role}', [RoleController::class, 'show']);
        Route::put('roles/{role}', [RoleController::class, 'update']);
        Route::delete('roles/{role}', [RoleController::class, 'destroy']);




        Route::get('service-categories/{id}', [ServiceCategoryController::class, 'show']);
        Route::post('service-categories', [ServiceCategoryController::class, 'store']);
        Route::put('service-categories/{id}', [ServiceCategoryController::class, 'update']);
        Route::delete('service-categories/{id}', [ServiceCategoryController::class, 'destroy']);

    });




    
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
    
    Route::get('/offers/public', [OfferController::class, 'publicOffers']);
    Route::get('/offers/private', [OfferController::class, 'privateOffers']);
    Route::get('/projects/{project}/offers/{offer}', [OfferController::class, 'show']);
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

    Route::get('projects/{project}/documents', [DocumentController::class, 'getProjectDocuments']);
    Route::post('projects/{project}/documents', [DocumentController::class, 'storeProjectDocument']);
    Route::delete('documents/{id}', [DocumentController::class, 'destroyProjectDocument']);

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