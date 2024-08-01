<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantsGroupedById extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
     
        return [
            'applicant' => [
                "id"=>$this->id,
                'user_id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                "assessment_score" => $this->assessment_score,
                "practical_score" => $this->practical_score,
                "interview_score" => $this->interview_score,
                'status' => $this->status,
                "application_date"=>$this->created_at->format('d/m/Y'),
                "updated_at"=>$this->created_at->format('d/m/Y'),
                // Add any other user details you want to include
                
            ],
          
        ];
    }
}
