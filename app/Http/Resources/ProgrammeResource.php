<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgrammeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'programme_id' => (string) $this->programme_id,
            'programme' => $this->programme,
            'programme_duration' => $this->programme_duration,
            'department_id_FK' => $this->department_id_FK,
            'adms_duration_1' => $this->adms_duration_1,
            'adms_duration_2' => $this->adms_duration_2,
            'adms_degree' => $this->adms_degree,
            'published' => $this->published,
        ];
    }
}
