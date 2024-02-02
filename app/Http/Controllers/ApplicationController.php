<?php

namespace App\Http\Controllers;

use App\Http\Resources\BatchesResource;
use App\Models\ScreeningSchedule;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function get_screening_schedule_in_session(Request $request){

        // return ScreeningSchedule::all();

        return BatchesResource::collection(ScreeningSchedule::all());
    }
}
