<?php

namespace App\Http\Controllers;

use App\Imports\LoadPUTMEScore;
use App\Models\Application;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class AdminController extends Controller
{
    
    public function update_applicant_scores(Request $request){

        $validate = Validator::make($request->all(),[
            'application_id'=> 'required', 'post_ume_subject1_score'=> 'required', 'post_ume_subject2_score'=> 'required', 
            'post_ume_subject3_score'=> 'required', 'post_ume_subject4_score'=>'required',
        ]);

        if($validate->fails()){
            return response()->json(['status_code'=>400, 'msg'=>'All fields are required with the following names: 
            application_id,post_ume_subject1_score,post_ume_subject2_score,post_ume_subject3_score,post_ume_subject4_score']);
        }

        $application = Application::where('application_id', $request->application_id)->first();
        if($application){
            $application->post_ume_subject1_score = $request->post_ume_subject1_score;
            $application->post_ume_subject2_score = $request->post_ume_subject2_score;
            $application->post_ume_subject3_score = $request->post_ume_subject3_score;
            $application->post_ume_subject4_score = $request->post_ume_subject4_score;
            $application->avg_ume_pume_score = $this->cal_putme_avg($application->ume_score, $request->post_ume_subject1_score, $request->post_ume_subject2_score,$request->post_ume_subject3_score,$request->post_ume_subject4_score);
            $application->save();
            return  response(['status'=>'success','message'=>'Scores updated successfully', 'data' => '']);
            
        }
        else{
            return  response(['status'=>'failed','message'=>'application with id: ${$request->application_id} not found', 'data' => '']);
        }

    }

    
    public function bulk_send_admission_status_csv(Request $request){
        $validate = Validator::make($request->all(),['adms' => 'required|file|mimes:xlsx,csv', ]);
        if($validate->fails()){
            return response()->json(['status_code'=>400, 'msg'=>'Excel/CSV file is expected here']);
        }
        $admsData = Excel::toCollection(new LoadPUTMEScore, $request->file('adms'), null, \Maatwebsite\Excel\Excel::XLSX);
        if (!empty($admsData) && $admsData->count() > 0) {
                $multiArray = [];
                // Skip header row (assuming first row is header)
                $header = $admsData[0][0];
                $data = $admsData[0]->skip(1);
                // Process data in chunks for large datasets
                $chunk_data =  $data->chunk(1024);
                for ($i=0; $i < count($chunk_data); $i++) { 
                    foreach ($chunk_data[$i] as $key => $row)
                    {       $sArray = [];
                            for ($x=0; $x < count($row); $x++) { 
                                $sArray[$header[$x]] = $row[$x];
                            }
                            array_push($multiArray, $sArray);
                    }

                    
        }
        $http_req = Http::post('https://adms.run.edu.ng/codebehind/front_end_processor?admission_offer=112233',[
            'params' => $multiArray
        ]);
         if($http_req->successful()){
           return $http_req;
        }
    }
        return  response(['status'=>'failed','message'=>'Empty excel file sent']);
    }


    public function bulk_send_admission_status_to_server(Request $request){
        
        $http_req = Http::post('https://adms.run.edu.ng/codebehind/front_end_processor?admission_offer=112233',[
            'params' => $request->rowData 
        ]);
         if($http_req->successful()){
           return $http_req;
        }

    }


    public function send_admission_status_to_server(Request $request){

        $validate = Validator::make($request->all(),[
            'FORM_NUMBER'=> 'required', 'SESSION_ADMITTED'=> 'required', 'DATE_ADMITTED'=> 'required', 
            'NON_REFUNDABLE_DEPOSIT'=> 'required', 'RESUMPTION_DATE'=>'required', 'PROG_CODE'=> 'required',
             'SCORE'=> 'required', 'ADMITTED'=> 'required','LEVEL'=> 'required', 'DURATION_IN_NUM'=> 'required', 'DURATION_IN_WORD'=> 'required'
        ]);
    
        if($validate->fails()){
            return response()->json(['status_code'=>400, 'msg'=>'All fields are required with the following names: 
            FORM_NUMBER,SESSION_ADMITTED,DATE_ADMITTED,NON_REFUNDABLE_DEPOSIT,RESUMPTION_DATE,PROG_CODE,SCORE,
            ADMITTED,LEVEL,DURATION_IN_NUM,DURATION_IN_WORD']);
        }

        $array_data = [[ 'FORM_NUMBER' => $request->FORM_NUMBER, 'SESSION_ADMITTED' => $request->SESSION_ADMITTED, 
        'DATE_ADMITTED' => $request->DATE_ADMITTED, 'NON_REFUNDABLE_DEPOSIT' => $request->NON_REFUNDABLE_DEPOSIT, 
        'RESUMPTION_DATE' => $request->RESUMPTION_DATE, 'PROG_CODE' => $request->PROG_CODE,
         'SCORE' => $request->SCORE, 'ADMITTED' => $request->ADMITTED, 'LEVEL' => $request->LEVEL, 
        'DURATION_IN_NUM' => $request->DURATION_IN_NUM,  'DURATION_IN_WORD' => $request->DURATION_IN_WORD]];
        
        $http_req = Http::post('https://adms.run.edu.ng/codebehind/front_end_processor?admission_offer=112233',[
            'params' => $array_data
        ]);
         if($http_req->successful()){
           return $http_req;
        }

    }

    public function load_putm_scores_bulk(Request $request){
        
        $validate = Validator::make($request->all(),['putme_scores' => 'required|file|mimes:xlsx,csv', ]);
        if($validate->fails()){
            return response()->json(['status_code'=>400, 'msg'=>'Excel/CSV file is expected here']);
        }
    //    ini_set('memory_limit', '-1');
    // try {0
        $excelData = Excel::toCollection(new LoadPUTMEScore, $request->file('putme_scores'), null, \Maatwebsite\Excel\Excel::XLSX);
        if (!empty($excelData) && $excelData->count() > 0) {
            $failedRecords = [];
            // Skip header row (assuming first row is header)
            $data = $excelData[0]->skip(1);
            // Process data in chunks for large datasets
            $chunk_data =  $data->chunk(1024);
            $numberToCheck = [];
            $application = '';
            for ($i=0; $i < count($chunk_data); $i++) { 
                foreach ($chunk_data[$i] as $key => $row)
                {
                    $uniqueIdentifier = $row[1];
                    if (in_array($uniqueIdentifier, $numberToCheck)) {continue;}
                    $application = Application::where('application_id', $uniqueIdentifier)->first();
                    if ($application) {
                            $get_filtered_data = $chunk_data[$i]->filter(function($item) use ($application){
                               return  $item[1] === $application->application_id;
                            });
                            $score1 = $score2 = $score3 = $score4 = 0;
                                foreach ($get_filtered_data as $key => $row1)
                                {
                                $formattedScore = $this->format_score_from_excel_upload($row1[4]);

                                // if(trim($row1[0]) == 'GENERAL MATHEMATICS'){ $formattedScore = $formattedScore+3; }
                                // elseif(trim($row1[0]) == 'SPECIAL MATHEMATICS'){$formattedScore = $formattedScore+3; }
                                // elseif(trim($row1[0]) == 'GENERAL PAPER'){$formattedScore = $formattedScore+2; }
                                // elseif(trim($row1[0]) == 'PHYSICS'){$formattedScore = $formattedScore+2; }
                                // elseif(trim($row1[0]) == 'CHEMISTRY'){$formattedScore = $formattedScore+3; }
                                $application->last_updated_date = date('Y-m-d H:i:s');
                        //     // Update each column based on different conditions
                                if (trim($row1[0]) == trim($application->post_ume_subject1)) {
                                    $application->post_ume_subject1_score = $formattedScore;
                                    $score1 = $formattedScore;
                                }
                    
                                elseif (trim($row1[0]) == trim($application->post_ume_subject2)) {
                                    $application->post_ume_subject2_score = $formattedScore;
                                    $score2 = $formattedScore;
                                }
                
                                elseif (trim($row1[0]) == trim($application->post_ume_subject3)) {
                                    $application->post_ume_subject3_score = $formattedScore;
                                    $score3 = $formattedScore;
                                }
                                
                                elseif (trim($row1[0]) == trim($application->post_ume_subject4)) {
                                    $application->post_ume_subject4_score = $formattedScore;
                                    $score4 = $formattedScore;
                                }
                                }
                        //     // Save the changes to the database
                                if($application->ume_score > 0){
                                    $application->avg_ume_pume_score = $this->cal_putme_avg($application->ume_score, $score1, $score2,$score3,$score4);
                                }
                                $application->save();
                                array_push($numberToCheck, $uniqueIdentifier);
                        } else {
                        //     // Handle case where application is not found
                            array_push($failedRecords, $row[1]."===".$row[2]);
                        }
                    
                }
            }
    
          return  response(['status'=>'success','message'=>'Scores uploaded successfully', 'data' => $failedRecords]);
            
            
        }
        return  response(['status'=>'failed','message'=>'Empty excel file sent']);
    // } catch (\Throwable $th) {
    //     return  response(['status'=>'failed','message'=>'Error with excel file sent']);
    // }
   
       
    }






    public function format_score_from_excel_upload(String $score){

        if (preg_match('/(\d+)\.\d+/', $score, $matches)) {
            $wholeNumber = $matches[1];
            return $wholeNumber; // Output: 7
        } else {
            return "No match found";
        }
    }

    public function cal_putme_avg($ume, $score1, $score2, $score3, $score4){
        $ume =    (float) $ume;
        $score1 = (float) $score1;
        $score2 = (float) $score2;
        $score3 = (float) $score3;  
        $score4 = (float) $score4;
        $sum_putme_score = $score1 +  $score2 + $score3 +  $score4;
        if( $ume != 0 && $sum_putme_score !=0){
            $ume_score_in_percent = $ume/8;
            $putme_score_in_percent = (($sum_putme_score/75) * 50);
           $ans =   $ume_score_in_percent + $putme_score_in_percent;
             return number_format($ans, 2, '.', '');
        }
      return 0;
    }


    










}
