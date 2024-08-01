<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonalDetailsResource extends JsonResource
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
            "firstname"=>$this->firstname,
            "lastname"=>$this->lastname,
            "nationalId"=>$this->nationalId,
            "address"=>$this->address,
            "gender"=>$this->gender,
            "contactNo"=>$this->contactNo,
            "created_at"=>$this->created_at->format('d/m/Y'),
            "updated_at"=>$this->created_at->format('d/m/Y'),
        ];
    }
}
