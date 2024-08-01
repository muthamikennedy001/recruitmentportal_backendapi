<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionalQualificationsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    { return [
        "id"=>$this->id,
        "user_id" => $this->user_id,
        "institution" => $this->institution,
        "body" => $this->body,
        "award" => $this->award,
        "professionalCertificate" => $this->professionalCertificate,
        "created_at"=>$this->created_at->format('d/m/Y'),
        "updated_at"=>$this->created_at->format('d/m/Y'),
    ];
    }
}
