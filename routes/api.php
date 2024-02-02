<?php

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


// 

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
