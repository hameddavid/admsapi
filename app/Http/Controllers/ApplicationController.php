<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetApplicationGivenBatchId;
use App\Http\Requests\GetApplicationGivenBatchIdAndCategory;
use App\Http\Resources\AllApplicationResource;
use App\Http\Resources\BatchesResource;
use App\Models\Application;
use App\Models\ScreeningSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    public function get_screening_schedule_in_session(Request $request){

        $config = DB::table('t_config')->select('_current_session_FK')->first();
        $subApp = DB::table('t_applications')->select('id_of_screening_schedule')
        ->distinct()->where('session_id_FK',$config->_current_session_FK)
        ->pluck('id_of_screening_schedule');
        return BatchesResource::collection(DB::table('t_screening_schedules')->select('*')
        ->whereIn('t_screening_schedules.id', $subApp)
        ->orderBy('t_screening_schedules.id')->get());
    }


    public function get_all_application_in_session(Request $request){

        $config = DB::table('t_config')->select('_current_session_FK')->first();
        $all_applications = Application::where('session_id_FK', $config->_current_session_FK)->get();
        // return $all_applications;
        return AllApplicationResource::collection($all_applications);
    }


    public function get_application_given_screen_batch_id(GetApplicationGivenBatchId $request){
        $request->validated($request->all()); 
        $config = DB::table('t_config')->select('_current_session_FK')->first();
        $all_applications = Application::where('session_id_FK', $config->_current_session_FK)
        ->where('id_of_screening_schedule', $request->batchId)
        ->orderBy('first_choice_programme_FK', 'asc')->orderBy('avg_ume_pume_score', 'desc')->get();
        return AllApplicationResource::collection($all_applications);
    }


    public function get_application_given_screen_batch_id_and_category(Request $request){
       
        // $validate = Validator::make($request->all(),[ 'batchId' => 'required','category' => 'required']);
        // if($validate->fails()){
        //     return response()->json(['status_code'=>400, 'msg'=>'All fields are required with the following names:  batchId, category']);
        // }
       
        $config = DB::table('t_config')->select('_current_session_FK')->first();
        $jamb_cutoff_ = 170;
        if($request->category == 'UME'){
            if($request->jambScore >= $jamb_cutoff_   && $request->filled("jambScore")){
                if( $request->filled('batchId')){
                    if( $request->filled('progID')){
                        $all_applications = Application::where('session_id_FK', $config->_current_session_FK)
                        ->join('t_applicants', 't_applications.last_updated_by', '=', 't_applicants.jamb_no')
                        ->where('id_of_screening_schedule', $request->batchId)
                        ->where('ume_score','>=', $request->jambScore )
                        ->where('first_choice_programme_FK', $request->progID )
                        ->where('app_category', 'UME')
                        ->select('t_applications.*', 't_applicants.date_of_birth')
                        ->orderBy('first_choice_programme_FK', 'asc')->orderBy('avg_ume_pume_score', 'desc')->get();
                        return AllApplicationResource::collection($all_applications);
                    }
                    $all_applications = Application::where('session_id_FK', $config->_current_session_FK)
                    ->join('t_applicants', 't_applications.last_updated_by', '=', 't_applicants.jamb_no')
                    ->where('id_of_screening_schedule', $request->batchId)
                    ->where('ume_score','>=', $request->jambScore )
                    ->where('app_category', 'UME')
                    ->select('t_applications.*', 't_applicants.date_of_birth')
                    ->orderBy('first_choice_programme_FK', 'asc')->orderBy('avg_ume_pume_score', 'desc')->get();
                    return AllApplicationResource::collection($all_applications);
                }
                if( $request->filled('progID')){
                    $all_applications = Application::where('session_id_FK', $config->_current_session_FK)
                    ->join('t_applicants', 't_applications.last_updated_by', '=', 't_applicants.jamb_no')
                    ->where('ume_score','>=', $request->jambScore )
                    ->where('first_choice_programme_FK', $request->progID )
                    ->where('app_category', 'UME')
                    ->select('t_applications.*', 't_applicants.date_of_birth')
                    ->orderBy('first_choice_programme_FK', 'asc')->orderBy('avg_ume_pume_score', 'desc')->get();
                    return AllApplicationResource::collection($all_applications);
                }
                $all_applications = Application::where('session_id_FK', $config->_current_session_FK)
                ->join('t_applicants', 't_applications.last_updated_by', '=', 't_applicants.jamb_no')
                ->where('ume_score','>=', $request->jambScore )
                ->where('app_category', 'UME')
                ->select('t_applications.*', 't_applicants.date_of_birth')
                ->orderBy('first_choice_programme_FK', 'asc')->orderBy('avg_ume_pume_score', 'desc')->get();
                return AllApplicationResource::collection($all_applications);
            }
            elseif($request->jambScore < $jamb_cutoff_  && $request->filled("jambScore")){
                if( $request->filled('progID')){
                    $all_applications = Application::where('session_id_FK', $config->_current_session_FK)
                    ->join('t_applicants', 't_applications.last_updated_by', '=', 't_applicants.jamb_no')
                    ->where('ume_score','<', $jamb_cutoff_  )
                    ->where('first_choice_programme_FK', $request->progID )
                    ->where('app_category', 'UME')
                    ->select('t_applications.*', 't_applicants.date_of_birth')
                    ->orderBy('first_choice_programme_FK', 'asc')->orderBy('avg_ume_pume_score', 'desc')->get();
                    return AllApplicationResource::collection($all_applications);
                }
                $all_applications = Application::where('session_id_FK', $config->_current_session_FK)
                ->join('t_applicants', 't_applications.last_updated_by', '=', 't_applicants.jamb_no')
                ->where('ume_score','<', $jamb_cutoff_  )
                ->where('app_category', 'UME')
                ->select('t_applications.*', 't_applicants.date_of_birth')
                ->orderBy('first_choice_programme_FK', 'asc')->orderBy('avg_ume_pume_score', 'desc')->get();
                return AllApplicationResource::collection($all_applications);
            }else{
                if( $request->filled('progID')){
                    $all_applications = Application::where('session_id_FK', $config->_current_session_FK)
                    ->join('t_applicants', 't_applications.last_updated_by', '=', 't_applicants.jamb_no')
                    ->where('first_choice_programme_FK', $request->progID )
                    ->where('app_category', 'UME')
                    ->select('t_applications.*', 't_applicants.date_of_birth')
                    ->orderBy('first_choice_programme_FK', 'asc')->orderBy('avg_ume_pume_score', 'desc')->get();
                    return AllApplicationResource::collection($all_applications);
                }
                $all_applications = Application::where('session_id_FK', $config->_current_session_FK)
                ->join('t_applicants', 't_applications.last_updated_by', '=', 't_applicants.jamb_no')
                ->where('app_category', 'UME')
                ->select('t_applications.*', 't_applicants.date_of_birth')
                ->orderBy('first_choice_programme_FK', 'asc')->orderBy('avg_ume_pume_score', 'desc')->get();
                return AllApplicationResource::collection($all_applications);
            }
        }
        elseif($request->category == 'DIRECT'){
            if( $request->filled('progID')){
                $all_applications = Application::where('session_id_FK', $config->_current_session_FK)
                ->join('t_applicants', 't_applications.last_updated_by', '=', 't_applicants.jamb_no')
                ->where('app_category', 'DIRECT')
                ->where('first_choice_programme_FK', $request->progID )
                ->select('t_applications.*', 't_applicants.date_of_birth')
                ->orderBy('first_choice_programme_FK', 'asc')->orderBy('avg_ume_pume_score', 'desc')->get();
                return AllApplicationResource::collection($all_applications);
            }
            $all_applications = Application::where('session_id_FK', $config->_current_session_FK)
            ->join('t_applicants', 't_applications.last_updated_by', '=', 't_applicants.jamb_no')
            ->where('app_category', 'DIRECT')
            ->select('t_applications.*', 't_applicants.date_of_birth')
            ->orderBy('first_choice_programme_FK', 'asc')->orderBy('avg_ume_pume_score', 'desc')->get();
            return AllApplicationResource::collection($all_applications);
        }
        elseif($request->category == 'TRANSFER'){
            if( $request->filled('progID')){
                $all_applications = Application::where('session_id_FK', $config->_current_session_FK)
                ->join('t_applicants', 't_applications.last_updated_by', '=', 't_applicants.jamb_no')
                ->where('app_category', 'TRANSFER')
                ->where('first_choice_programme_FK', $request->progID )
                ->select('t_applications.*', 't_applicants.date_of_birth')
                ->orderBy('first_choice_programme_FK', 'asc')->orderBy('avg_ume_pume_score', 'desc')->get();
                return AllApplicationResource::collection($all_applications);
            }
            $all_applications = Application::where('session_id_FK', $config->_current_session_FK)
            ->join('t_applicants', 't_applications.last_updated_by', '=', 't_applicants.jamb_no')
            ->where('app_category', 'TRANSFER')
            ->select('t_applications.*', 't_applicants.date_of_birth')
            ->orderBy('first_choice_programme_FK', 'asc')->orderBy('avg_ume_pume_score', 'desc')->get();
            return AllApplicationResource::collection($all_applications);
        }
        else{

            return  response(['status'=>'failed','message'=>'application with category: '. $request->category .' not found', 'data' => '']);

        }
       
    }
}
