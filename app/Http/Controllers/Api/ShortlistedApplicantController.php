<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ApplicantsGroupedById;
use App\Http\Resources\ShortlistedApplicantResource;
use App\Models\AssessmentAttempt;
use App\Models\ShortlistedApplicants;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ShortlistedApplicantController extends Basecontroller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $shortlistedApplicants = ShortlistedApplicants::with('user')->get(); // Eager load user data
        return $this->sendResponse(ShortlistedApplicantResource::collection($shortlistedApplicants), 'Shortlisted Applicant Details Retrieved Successfully');
    }

    /**
     * Display a listing of the resource.
     * 
     * @OA\Get(
     *     path="/api/user/shortlistedapplicants",
     *     summary="Get shortlisted applicants",
     *     tags={"Shortlisted Applicants"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *              @OA\Items(ref="#/components/schemas/ApplicantsGroupedById")
     *         )
     *     )
     * )
     */




    public function indexGroupedByJob(): JsonResponse
    {
        // Fetch all shortlisted applicants with their associated user data
        $shortlistedApplicants = ShortlistedApplicants::with('user')->get();

        // Group applicants by job_id
        $groupedApplicants = $shortlistedApplicants->groupBy('job_id');

        // Prepare the response structure
        $response = [];
        foreach ($groupedApplicants as $jobId => $applicants) {
            $response[] = [
                'job_id' => $jobId,
                'applicants' => ApplicantsGroupedById::collection($applicants)
            ];
        }

        return $this->sendResponse(ApplicantsGroupedById::collection($response), 'Shortlisted Applicant Details Retrieved Successfully');
    }


    /**
     * @OA\Post(
     *     path="/api/user/shortlistedapplicants",
     *     summary="Shortlist a new applicant",
     *     tags={"Shortlisted Applicants"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"job_id", "assessment_id", "assessment_score"},
     *             @OA\Property(property="job_id", type="integer"),
     *             @OA\Property(property="assessment_id", type="integer"),
     *             @OA\Property(property="assessment_score", type="number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Applicant Shortlisted Successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/ShortlistedApplicant")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
        $user = Auth::user();

        // Validate input
        $validator = Validator::make($input, [
            'job_id' => 'required',
            'assessment_id' => 'required',
            'assessment_score' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create new assessment attempt
        $shortlistedApplicant = $user->shortlistedApplicants()->create([
            'job_id' => $request->job_id,
            'assessment_id' => $request->assessment_id,
            'assessment_score' => $request->assessment_score,
            'status' => 'In Review',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Applicant Shorlisted Successfully',
            'data' => new ShortlistedApplicantResource($shortlistedApplicant)
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/user/shortlistedapplicants/{id}",
     *     summary="Get a specific shortlisted applicant",
     *     tags={"Shortlisted Applicants"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/ShortlistedApplicant")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Applicant Not Shortlisted"
     *     )
     * )
     */
    public function show($applicationId): JsonResponse

    {
        $id = $applicationId;
        $shortlistedApplicant = AssessmentAttempt::with('user')->find($id);


        if (is_null($shortlistedApplicant)) {
            return $this->sendError('Applicant Not Shortlisted');
        }

        return $this->sendResponse(new ShortlistedApplicantResource($shortlistedApplicant), 'Applicant Retrieved successfully');
    }


    /**
     * @OA\Put(
     *     path="/api/user/shortlistedapplicants/{id}",
     *     summary="Update a specific shortlisted applicant",
     *     tags={"Shortlisted Applicants"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="job_id", type="integer"),
     *             @OA\Property(property="assessment_id", type="integer"),
     *             @OA\Property(property="assessment_score", type="number"),
     *             @OA\Property(property="status", type="string", enum={"In Review", "Approved", "Rejected", "Hired"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assessment Attempt Updated Successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/ShortlistedApplicant")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shortlisted Applicant Details Not Found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request): JsonResponse
    {
        // Validate input, making all fields nullable to allow for partial updates, including id
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:shortlisted_applicants,id',
            'job_id' => 'nullable',
            'assessment_id' => 'nullable',
            'score' => 'nullable|numeric',
            'status' => 'nullable|in:In Review,Approved,Rejected,Hired'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Extract id from the request
        $id = $request->input('id');

        // Find the assessment attempt
        $shortlistedApplicant = ShortlistedApplicants::find($id);

        if (is_null($shortlistedApplicant)) {
            return response()->json([
                'success' => false,
                'message' => 'Shortlisted Applicant Details Not Found.'
            ], 404);
        }

        // Update fields only if they are provided in the request
        if ($request->has('job_id')) {
            $shortlistedApplicant->job_id = $request->input('job_id');
        }

        if ($request->has('assessment_id')) {
            $shortlistedApplicant->assessment_id = $request->input('assessment_id');
        }


        if ($request->has('assessment_score')) {
            $shortlistedApplicant->assessment_score = $request->assessment_score;
        }

        if ($request->has('practical_score')) {
            $shortlistedApplicant->practical_score = $request->practical_score;
        }
        if ($request->has('interview_score')) {
            $shortlistedApplicant->interview_score = $request->interview_score;
        }

        if ($request->has('status')) {
            $shortlistedApplicant->status = $request->input('status');
        }

        // Save the updated assessment attempt
        $shortlistedApplicant->save();

        return response()->json([
            'success' => true,
            'message' => 'Assessment Attempt Updated Successfully.',
            'data' => new ApplicantsGroupedById($shortlistedApplicant)
        ]);
    }



    /**
     * @OA\Delete(
     *     path="/api/user/shortlistedapplicants/{id}",
     *     summary="Delete a specific shortlisted applicant",
     *     tags={"Shortlisted Applicants"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assessment Attempt Deleted Successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Assessment Attempt Not Found"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        // Find the assessment attempt
        $shortlistedApplicant = ShortlistedApplicants::find($id);

        if (is_null($shortlistedApplicant)) {
            return response()->json([
                'success' => false,
                'message' => 'User Shorlisted Details Not Found.'
            ], 404);
        }

        // Delete the assessment attempt
        $shortlistedApplicant->delete();

        return response()->json([
            'success' => true,
            'message' => 'User Shorlisted Details Deleted Successfully.'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/user/applicants-by-job",
     *     summary="Get applicants by job",
     *     tags={"Shortlisted Applicants"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"job_id"},
     *             @OA\Property(property="job_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Applicants Retrieved Successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ApplicantsGroupedById")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function getApplicantsByJob(Request $request): JsonResponse
    {
        $jobId = $request->input('job_id');

        // Validate the job_id
        $validator = Validator::make($request->all(), [
            'job_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Fetch applicants by job_id
        $shortlistedApplicants = ShortlistedApplicants::with('user')
            ->where('job_id', $jobId)
            ->orderBy('assessment_score', 'desc') // or 'asc' for ascending order
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Applicants Retrieved Successfully',
            'data' => ApplicantsGroupedById::collection($shortlistedApplicants)
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/user/applications-by-user",
     *     summary="Get applications by a specific user",
     *     tags={"Shortlisted Applicants"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id"},
     *             @OA\Property(property="user_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Applications Retrieved Successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ShortlistedApplicant")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function getApplicationsByUser(Request $request): JsonResponse
    {
        $userId = $request->input('user_id');

        // Validate the user_id
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Fetch applications by user_id
        $applications = ShortlistedApplicants::with('user')
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Applications Retrieved Successfully',
            'data' => ShortlistedApplicantResource::collection($applications)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/user/my-applications",
     *     summary="Get applications by the authenticated user",
     *     tags={"Shortlisted Applicants"},
     *     @OA\Response(
     *         response=200,
     *         description="Applications Retrieved Successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ShortlistedApplicant")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated"
     *     )
     * )
     */
    public function getApplicationsByUserId(Request $request): JsonResponse
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Ensure the user is authenticated
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }

        // Fetch applications by authenticated user_id
        $applications = ShortlistedApplicants::with('user')
            ->where('user_id', $user->id)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Applications Retrieved Successfully',
            'data' => ShortlistedApplicantResource::collection($applications)
        ]);
    }
}
