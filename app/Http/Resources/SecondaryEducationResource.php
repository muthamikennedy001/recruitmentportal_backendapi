<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SecondaryEducationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "user_id" => $this->user_id,
            "school" => $this->school,
            "kcseYear" => $this->kcseYear,
            "grade" => $this->grade,
            "kcseCertificate" => $this->kcseCertificate,
            "created_at"=>$this->created_at->format('d/m/Y'),
            "updated_at"=>$this->created_at->format('d/m/Y'),
        ];
    }
}
