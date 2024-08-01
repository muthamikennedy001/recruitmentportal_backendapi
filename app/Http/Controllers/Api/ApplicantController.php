<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ApplicantResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Applicant Details",
 *     description="endpoints for managing Applicant Details"
 * )
 */

class ApplicantController extends Basecontroller
{
    /**
     * @OA\Get(
     *     path="/api/applicants",
     *     summary="Get all applicants",
     *     tags={"Applicants"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *            
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */

    public function index(): JsonResponse
    {
        // Retrieve all users with their related details
        $applicantDetails = User::with([
            'personalDetails',
            'highestEducationLevels',
            'secondaryEducation',
            'professionalQualifications'
        ])->get();

        // Transform user data using ApplicantResource
        $transformedData = ApplicantResource::collection($applicantDetails);

        // Return response using BaseController's sendResponse
        return $this->sendResponse($transformedData, 'Details Retrieved Successfully');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreApplicantRequest $request)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/applicants/{applicant}",
     *     summary="Get a specific applicant",
     *     tags={"Applicants"},
     *     @OA\Parameter(
     *         name="applicant",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Applicant ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/Applicant"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Applicant not found"
     *     )
     * )
     */
    public function show(User $applicant): JsonResponse
    {
        // Load relationships
        $applicant->load([
            'personalDetails',
            'highestEducationLevels',
            'secondaryEducation',
            'professionalQualifications'
        ]);

        // Transform user data using ApplicantResource
        $applicantResource = new ApplicantResource($applicant);

        // Return response using BaseController's sendResponse
        return $this->sendResponse($applicantResource, 'User Details Retrieved Successfully');
    }
}
