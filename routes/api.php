<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\HelperController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('get-applicants-in-session', [ApplicantController::class, 'get_all_applicants_in_session']);
Route::get('get-screening-schedule-in-session', [ApplicationController::class, 'get_screening_schedule_in_session']);
Route::get('get-all-application-in-session', [ApplicationController::class, 'get_all_application_in_session']);
Route::get('get-application-given-screen-batch-id', [ApplicationController::class, 'get_application_given_screen_batch_id']);
Route::get('get-application-given-screen-batch-id-and-category', [ApplicationController::class, 'get_application_given_screen_batch_id_and_category']);
Route::get('get-programmes', [HelperController::class, 'get_available_programmes_with_properties']); //
Route::any('load-putm-scores-bulk', [AdminController::class, 'load_putm_scores_bulk']);
Route::post('send-adms-status', [AdminController::class, 'send_admission_status_to_server']);
Route::post('update-applicant-scores', [AdminController::class, 'update_applicant_scores']);






// 

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
