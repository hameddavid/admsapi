<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'center_name' => $this->center_name,
            'center_address' => $this->center_name,
            'schedule_date' => $this->schedule_date,
            'schedule_time' => $this->schedule_time,
        ];
    }
}
