<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShortlistedApplicantResource extends JsonResource
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
            "job_id" => $this->job_id,
            'applicant' => [
                'user_id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                "assessment_score" => $this->assessment_score,
                "practical_score" => $this->practical_score,
                "interview_score" => $this->interview_score,
                'status' => $this->status,
                // Add any other user details you want to include
            ],
            "application_date"=>$this->created_at->format('d/m/Y'),
            "updated_at"=>$this->created_at->format('d/m/Y'),

        ];
    }
}
