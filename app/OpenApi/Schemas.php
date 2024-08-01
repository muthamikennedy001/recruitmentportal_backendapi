<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

class Schemas
{
    /**
     * @OA\Schema(
     *     schema="PersonalDetails",
     *     type="object",
     *     required={"id", "firstname", "lastname", "nationalId", "address", "gender", "contactNo"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="firstname", type="string", example="John"),
     *     @OA\Property(property="lastname", type="string", example="Doe"),
     *     @OA\Property(property="nationalId", type="string", example="123456789"),
     *     @OA\Property(property="address", type="string", example="123 Main St, Anytown, USA"),
     *     @OA\Property(property="gender", type="string", example="Male"),
     *     @OA\Property(property="contactNo", type="string", example="+123456789")
     * )
     */
    public function personalDetails()
    {
    }

    /**
     * @OA\Schema(
     *     schema="HighestEducationLevel",
     *     type="object",
     *     required={"id", "user_id", "institution", "course", "graduationYear", "grade", "certificate"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="user_id", type="integer", example=1),
     *     @OA\Property(property="institution", type="string", example="University of Example"),
     *     @OA\Property(property="course", type="string", example="Computer Science"),
     *     @OA\Property(property="graduationYear", type="integer", example=2020),
     *     @OA\Property(property="grade", type="string", example="A"),
     *     @OA\Property(property="certificate", type="string", example="Bachelor's Degree")
     * )
     */
    public function highestEducationLevel()
    {
    }

    /**
     * @OA\Schema(
     *     schema="SecondaryEducation",
     *     type="object",
     *     required={"id", "user_id", "school", "kcseYear", "grade", "kcseCertificate"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="user_id", type="integer", example=1),
     *     @OA\Property(property="school", type="string", example="High School Example"),
     *     @OA\Property(property="kcseYear", type="integer", example=2010),
     *     @OA\Property(property="grade", type="string", example="A"),
     *     @OA\Property(property="kcseCertificate", type="string", example="Certificate of KCSE")
     * )
     */
    public function secondaryEducation()
    {
    }

    /**
     * @OA\Schema(
     *     schema="ProfessionalQualifications",
     *     type="object",
     *     required={"id", "user_id", "institution", "body", "award", "professionalCertificate"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="user_id", type="integer", example=1),
     *     @OA\Property(property="institution", type="string", example="Institution Example"),
     *     @OA\Property(property="body", type="string", example="Professional Body Example"),
     *     @OA\Property(property="award", type="string", example="Professional Award"),
     *     @OA\Property(property="professionalCertificate", type="string", example="Certificate Example")
     * )
     */
    public function professionalQualifications()
    {
    }

    /**
     * @OA\Schema(
     *     schema="Applicant",
     *     type="object",
     *     required={"user_id", "name", "email"},
     *     @OA\Property(property="user_id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="John Doe"),
     *     @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *     @OA\Property(
     *         property="personal_details",
     *         ref="#/components/schemas/PersonalDetails"
     *     ),
     *     @OA\Property(
     *         property="highest_education_level",
     *         ref="#/components/schemas/HighestEducationLevel"
     *     ),
     *     @OA\Property(
     *         property="secondary_education",
     *         ref="#/components/schemas/SecondaryEducation"
     *     ),
     *     @OA\Property(
     *         property="professional_qualifications",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/ProfessionalQualifications")
     *     )
     * )
     */
    public function applicant()
    {
    }

    /**
     * @OA\Schema(
     *     schema="ApplicantsGroupedById",
     *     type="object",
     *     required={"applicant"},
     *     @OA\Property(
     *         property="applicant",
     *         type="object",
     *         required={"id", "user_id", "name", "email", "assessment_score", "practical_score", "interview_score", "status", "application_date", "updated_at"},
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="user_id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="John Doe"),
     *         @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *         @OA\Property(property="assessment_score", type="number", format="float", example=85.5),
     *         @OA\Property(property="practical_score", type="number", format="float", example=90.0),
     *         @OA\Property(property="interview_score", type="number", format="float", example=88.0),
     *         @OA\Property(property="status", type="string", example="Pending"),
     *         @OA\Property(property="application_date", type="string", format="date", example="2023-05-01"),
     *         @OA\Property(property="updated_at", type="string", format="date", example="2023-05-02")
     *     )
     * )
     */
    public function applicantsGroupedById()
    {
    }

    /**
     * @OA\Schema(
     *     schema="AssessmentAttempt",
     *     type="object",
     *     required={"id", "user_id", "job_id", "assessment_score", "practical_score", "interview_score", "status", "created_at", "updated_at"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="user_id", type="integer", example=1),
     *     @OA\Property(property="job_id", type="integer", example=1),
     *     @OA\Property(property="assessment_score", type="number", format="float", example=85.5),
     *     @OA\Property(property="practical_score", type="number", format="float", example=90.0),
     *     @OA\Property(property="interview_score", type="number", format="float", example=88.0),
     *     @OA\Property(property="status", type="string", example="Pending"),
     *     @OA\Property(property="created_at", type="string", format="date", example="2023-05-01"),
     *     @OA\Property(property="updated_at", type="string", format="date", example="2023-05-02")
     * )
     */
    public function assessmentAttempt()
    {
    }


    /**
     * @OA\Schema(
     *     schema="ShortlistedApplicant",
     *     type="object",
     *     required={"id", "job_id", "applicant", "application_date", "updated_at"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="job_id", type="integer", example=101),
     *     @OA\Property(
     *         property="applicant",
     *         type="object",
     *         required={"user_id", "name", "email", "assessment_score", "practical_score", "interview_score", "status"},
     *         @OA\Property(property="user_id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="John Doe"),
     *         @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *         @OA\Property(property="assessment_score", type="number", format="float", example=85.5),
     *         @OA\Property(property="practical_score", type="number", format="float", example=90.0),
     *         @OA\Property(property="interview_score", type="number", format="float", example=88.0),
     *         @OA\Property(property="status", type="string", example="Shortlisted")
     *     ),
     *     @OA\Property(property="application_date", type="string", format="date", example="2023-05-01"),
     *     @OA\Property(property="updated_at", type="string", format="date", example="2023-05-02")
     * )
     */
    public function shortlistedApplicant()
    {
    }
}
