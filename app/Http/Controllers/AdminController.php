<?php

namespace App\Http\Controllers;

use App\Imports\LoadPUTMEScore;
use App\Models\Application;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    

    public function load_putm_scores_bulk(Request $request){
      
       $in_data =  Excel::toCollection(null, $request->file('putme_scores'));
       foreach($in_data[0] as $row){
        $app = Application::where('application_id', $row[1])->first();
        if($app){
            
            echo  $app->high_school_attended ."<br>";
        }
       }
    }
}
