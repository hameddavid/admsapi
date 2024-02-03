<?php

namespace App\Http\Controllers;

use App\Http\Resources\AllApplicationResource;
use App\Http\Resources\BatchesResource;
use App\Models\Application;
use App\Models\ScreeningSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    public function get_screening_schedule_in_session(Request $request){

        $config = DB::table('t_config')->select('_current_session_FK')->first();
        $subApp = DB::table('t_applications')->select('id_of_screening_schedule')
        ->distinct()->where('session_id_FK',$config->_current_session_FK)->pluck('id_of_screening_schedule');
        return BatchesResource::collection(DB::table('t_screening_schedules')->select('*')->whereIn('t_screening_schedules.id', $subApp)->orderBy('t_screening_schedules.id')->get());
    }


    public function get_all_application_in_session(Request $request){

        $config = DB::table('t_config')->select('_current_session_FK')->first();
        $all_applications = Application::where('session_id_FK', $config->_current_session_FK)->get();
        // return $all_applications;
        return AllApplicationResource::collection($all_applications);
    }
}
