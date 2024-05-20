<?php

namespace App\Http\Controllers;

use App\Imports\LoadPUTMEScore;
use App\Models\Application;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Expr\Cast\String_;

class AdminController extends Controller
{
    

    public function load_putm_scores_bulk(Request $request){
    //    ini_set('memory_limit', '-1');
    try {
        $excelData = Excel::toCollection(null, $request->file('putme_scores'));
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
    } catch (\Throwable $th) {
        return  response(['status'=>'failed','message'=>'Error with excel file sent']);
    }
   
       
    }






    public function format_score_from_excel_upload(String $score){

        if (preg_match('/(\d+)\.\d+/', $score, $matches)) {
            $wholeNumber = $matches[1];
            return $wholeNumber; // Output: 7
        } else {
            return "No match found";
        }
    }
}
