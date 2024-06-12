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

        // $array_data = [[
        //     'FORM_NUMBER' => '18165', 'SESSION_ADMITTED' => '2024/2025', 'DATE_ADMITTED' => '2024-06-05',
        //     'NON_REFUNDABLE_DEPOSIT' => 'Fifty thousand naira (N50000)', 'RESUMPTION_DATE' => 'Monday, 16th  October 2024',
        //     'PROG_CODE' => 'CPE', 'SCORE' => '67.5', 'ADMITTED' => 'Y', 'LEVEL' => '300', 'DURATION_IN_NUM' => '3', 
        //     'DURATION_IN_WORD' => 'THREE'
        // ]];
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
        
        $request->validate(['putme_scores' => 'required|file|mimes:xlsx,csv', ]);
    //    ini_set('memory_limit', '-1');
    // try {0
        $excelData = Excel::toCollection(new LoadPUTMEScore, $request->file('putme_scores'), null, \Maatwebsite\Excel\Excel::XLSX);
        if (!empty($excelData) && $excelData->count() > 0) {
            $failedRecords = [];
            // Skip header row (assuming first row is header)
            $data = $excelData[0]->skip(1);
            // Process data in chunks for large datasets
            $chunk_data =  $data->chunk(1000);
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
                                foreach ($get_filtered_data as $key => $row1)
                                {
                                $formattedScore = $this->format_score_from_excel_upload($row1[4]);
                                $application->last_updated_date = date('Y-m-d H:i:s');
                        //     // Update each column based on different conditions
                                if (trim($row1[0]) == trim($application->post_ume_subject1)) {
                                    $application->post_ume_subject1_score = $formattedScore;
                                }
                    
                                elseif (trim($row1[0]) == trim($application->post_ume_subject2)) {
                                    $application->post_ume_subject2_score = $formattedScore;
                                }
                
                                elseif (trim($row1[0]) == trim($application->post_ume_subject3)) {
                                    $application->post_ume_subject3_score = $formattedScore;
                                }
                                
                                elseif (trim($row1[0]) == trim($application->post_ume_subject4)) {
                                    $application->post_ume_subject4_score = $formattedScore;
                                }
                                }
                        //     // Save the changes to the database
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



    public function update_applicant_putm_score(Request $request){

        $application = Application::where('application_id', $request->app_id)->first();
        if($application){
            //
            //     $application->last_updated_date = date('Y-m-d H:i:s');
            // //     // Update each column based on different conditions
            //         if (trim($row1[0]) == trim($application->post_ume_subject1)) {
            //             $application->post_ume_subject1_score = $formattedScore;
            //         }
        
            //         elseif (trim($row1[0]) == trim($application->post_ume_subject2)) {
            //             $application->post_ume_subject2_score = $formattedScore;
            //         }
    
            //         elseif (trim($row1[0]) == trim($application->post_ume_subject3)) {
            //             $application->post_ume_subject3_score = $formattedScore;
            //         }
                    
            //         elseif (trim($row1[0]) == trim($application->post_ume_subject4)) {
            //             $application->post_ume_subject4_score = $formattedScore;
            //         }
               // Save the changes to the database
                    $application->save();
        }
        else{
            return  response(['status'=>'failed','message'=>'No application found']);
        }
    }










}
