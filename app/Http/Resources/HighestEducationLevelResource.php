<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HighestEducationLevelResource extends JsonResource
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
            "institution" => $this->institution,
            "course" => $this->course,
            "graduationYear" => $this->graduationYear,
            "grade" => $this->grade,
            "certificate" => $this->certificate,
            "created_at"=>$this->created_at->format('d/m/Y'),
            "updated_at"=>$this->created_at->format('d/m/Y'),
        ];
    }
}
