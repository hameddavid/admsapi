<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProgrammeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HelperController extends Controller
{
    //

    public function get_available_programmes_with_properties(Request $request){
        $allProgs = DB::table('t_programmes')->select('*')->where('published',1)->get();
        return ProgrammeResource::collection($allProgs);
      
    }
}

