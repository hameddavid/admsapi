<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;


class ApplicantController extends Controller
{
    //
    public function get_all_applicants_in_session(Request $request){
        $app = Applicant::where('surname', 'ADEKOYA')->get();
    }
}
