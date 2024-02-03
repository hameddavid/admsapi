<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ApplicationController;
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
Route::post('load-putm-scores-bulk', [AdminController::class, 'load_putm_scores_bulk']);




// 

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
