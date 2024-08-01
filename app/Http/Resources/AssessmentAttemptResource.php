<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentAttemptResource extends JsonResource
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
            "job_id" => $this->job_id,
            "assessment_score" => $this->assessment_score,
            "practical_score" => $this->practical_score,
            "interview_score" => $this->interview_score,
            "status"=>$this->status,
            "created_at"=>$this->created_at->format('d/m/Y'),
            "updated_at"=>$this->created_at->format('d/m/Y'),
        ];
    }
}
