<?php

namespace App\Imports;

use App\Models\Application;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Http\Request;



class PUTMImport implements ToCollection,ToModel,WithHeadingRow
{

    private $request = '';

    public function __construct(Request $request){
        $this->request = $request;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Application([
            //
        ]);
    }


    public function collection(Collection $collection){ 
        //this loop use for group only 
        foreach($collection as $key => $value){
            // $user = new Application();
            // $user->user_name = $value['user_name'];
            // $user->user_email = $value['user_email'];
            // $user->save();
        }
    }


    public function rules(): array
    {
        return [
            // Can also use callback validation rules
            'application_id' => function ($attribute, $value, $onFailure) {
                if (empty($value)) {
                    $onFailure('application_id is empty or invalid please check and try again !');
                }
            }
        ];
    }
}
