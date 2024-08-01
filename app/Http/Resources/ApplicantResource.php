<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'personal_details' => $this->personalDetails ? [
                'id' => $this->personalDetails->id,
                'firstname' => $this->personalDetails->firstname,
                'lastname' => $this->personalDetails->lastname,
                'nationalId' => $this->personalDetails->nationalId,
                'address' => $this->personalDetails->address,
                'gender' => $this->personalDetails->gender,
                'contactNo' => $this->personalDetails->contactNo,
                //'user' => $this->getUserData()
            ] : null,
            'highest_education_level' => $this->highestEducationLevels ? [
                'id' => $this->highestEducationLevels->id,
                'institution' => $this->highestEducationLevels->institution,
                'course' => $this->highestEducationLevels->course,
                'graduationYear' => $this->highestEducationLevels->graduationYear,
                'grade' => $this->highestEducationLevels->grade,
                'certificate' => $this->highestEducationLevels->certificate,
                //'user' => $this->getUserData()
            ] : null,
            'secondary_education' => $this->secondaryEducation ? [
                'id' => $this->secondaryEducation->id,
                'school' => $this->secondaryEducation->school,
                'kcseYear' => $this->secondaryEducation->kcseYear,
                'grade' => $this->secondaryEducation->grade,
                'kcseCertificate' => $this->secondaryEducation->kcseCertificate,
                //'user' => $this->getUserData()
            ] : null,
            'professional_qualifications' => $this->professionalQualifications ? $this->professionalQualifications->map(function ($qualification) {
                return [
                    'id' => $qualification->id,
                    'institution' => $qualification->institution,
                    'body' => $qualification->body,
                    'award' => $qualification->award,
                    'professionalCertificate' => $qualification->professionalCertificate,
                    //'user' => $this->getUserData()
                ];
            }) : [],
        ];
    }

    /**
     * Get the user data to include in related details.
     *
     * @return array<string, mixed>
     */
    private function getUserData(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
