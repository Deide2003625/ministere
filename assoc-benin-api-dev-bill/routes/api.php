<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AccessController;
use App\Http\Controllers\UpdateAdminController;
use App\Http\Controllers\AdminSigningUpController;
use App\Http\Controllers\RequestCodeMailController;
use App\Http\Controllers\RecordingRequestsController;
use App\Http\Controllers\PasswordRecoveringController;
use App\Http\Controllers\RequestApprovedMailController;
use App\Http\Controllers\RequestObservationsController;
use App\Http\Controllers\RequestRejectedMailController;
use App\Http\Controllers\ObservationsManagementController;
use App\Http\Controllers\UpdateRecordingRequestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Create API

//[Godwin] => Routes accessible without being authenticated
############################ DASHBOARD ##############################################################    
Route::post("login",[AccessController::class, "Login"]); // Verified.
Route::get("give_access_to_reset/{token}",[PasswordRecoveringController::class, "ShowPasswordResetForm"]); // Verified.
Route::post("reset_password",[PasswordRecoveringController::class, "Recovering"]); // Verified.

############################ CLIENT ##############################################################
Route::post("make_request",[RecordingRequestsController::class, "MakeRequest"]); // Verified.
Route::post("update_request",[UpdateRecordingRequestController::class, "UpdateRequest"]); // Verified.

############################ DASHBOARD AND CLIENT ##############################################################
Route::get("get_record/{code}",[RecordingRequestsController::class, "GetRecord"]); // Verified.


//[Godwin] => Add laravel/sanctum middleware to protect routes
Route::middleware('auth:sanctum')->group(function () {

    ############################ DASHBOARD ##############################################################
    Route::post("logout",[AccessController::class, "Logout"]); // Verified.

    Route::post("sign_up",[AdminSigningUpController::class, "SignUp"]); // Verified.

    Route::get("get_admins",[AccessController::class, "GetAdmins"]); // Verified
    Route::get("get_super_admins/{asker}",[AccessController::class, "GetSuperAdmins"]); // Verified
    Route::get("get_admin/{code}",[AccessController::class, "GetAdmin"]); // Verified
    Route::get("get_super_admin/{code}/{asker}",[AccessController::class, "GetSuperAdmin"]); // Verified
    Route::get("freeze_admin/{code}/{status}/{asker}",[AccessController::class, "FreezeAdmin"]); // Verified
    
    Route::post("update_admin_data",[UpdateAdminController::class, "UpdateData"]); // Verified
    
    Route::get("get_in_progress/{admin_registration_number}",[RecordingRequestsController::class, "GetInProgress"]); // Verified.
    Route::get("get_approved/{admin_registration_number}",[RecordingRequestsController::class, "GetApproved"]); // Verified.
    Route::get("get_approving/{admin_registration_number}",[RecordingRequestsController::class, "GetApproving"]); // Verified.
    Route::get("get_rejecting/{admin_registration_number}",[RecordingRequestsController::class, "GetRejecting"]); // Verified.
    Route::get("get_rejected/{admin_registration_number}",[RecordingRequestsController::class, "GetRejected"]); // Verified.
    Route::get("count_requests",[RecordingRequestsController::class, "CountRequests"]); // Verified
    Route::get("freeze_association/{code}/{status}",[RecordingRequestsController::class, "FreezeAssociation"]); // Verified

    Route::get("group_by_department/{admin_registration_number}",[RecordingRequestsController::class, "GroupByDepartment"]);
    Route::post("pattern_checking",[RecordingRequestsController::class, "PatternChecking"]); // Verified
        
    Route::post("admin_observation",[RequestObservationsController::class, "AdminObservation"]); // Verified.
    
    Route::get("get_observations",[ObservationsManagementController::class, "Read"]); // Verified.
    Route::get("get_observation/{id}",[ObservationsManagementController::class, "ReadOne"]); // Verified.
    Route::post("writing_observation",[ObservationsManagementController::class, "WritingRequest"]); // Verified.
    
    
    # Not used
    Route::post("mail_code",[RequestCodeMailController::class, "Code"]); // 
    Route::post("mail_rejection",[RequestRejectedMailController::class, "Rejected"]); // 
    Route::post("mail_approving",[RequestApprovedMailController::class, "Approved"]); // 
    Route::post("mail_approving",[AdminRegistrationMailController::class, "Registration"]); //
    Route::get("count_modifications",[RecordingRequestsController::class, "CountModifications"]); // utilité ????
    
    ############################ CLIENT ##############################################################
    Route::get("delete_request/{code}/{asker}",[RecordingRequestsController::class, "DeleteRequest"]); // not used but verified.
}); 

