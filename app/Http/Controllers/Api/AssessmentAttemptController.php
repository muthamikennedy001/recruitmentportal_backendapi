<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ApplicantsGroupedById;
use App\Http\Resources\AssessmentAttemptResource;
use App\Models\AssessmentAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AssessmentAttemptController extends Basecontroller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/user/assessment-attempts",
     *     summary="Get all assessment attempts",
     *     tags={"Assessment Attempts"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AssessmentAttempt")),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $assessmentAttempts = AssessmentAttempt::all();
        return $this->sendResponse(AssessmentAttemptResource::collection($assessmentAttempts), 'Assessment Test Details Retrieved Successfully');
    }


    public function indexGroupedByJob(): JsonResponse
    {
        // Fetch all shortlisted applicants with their associated user data
        $jobApplicants = AssessmentAttempt::with('user')->get();


        // Group applicants by job_id
        $groupedApplicants = $jobApplicants->groupBy('job_id');

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
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/api/user/assessment-attempts",
     *     summary="Create a new assessment attempt",
     *     tags={"Assessment Attempts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="job_id", type="integer"),
     *             @OA\Property(property="assessment_id", type="integer"),
     *             @OA\Property(property="assessment_score", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/AssessmentAttempt")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
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
        $assessmentAttempt = $user->assessmentAttempts()->create([
            'job_id' => $request->job_id,
            'assessment_id' => $request->assessment_id,
            'assessment_score' => $request->assessment_score,
            'status' => 'In Review',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assessment results recorded Successfully',
            'data' => new AssessmentAttemptResource($assessmentAttempt)
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/user/assessment-attempts/{id}",
     *     summary="Get a specific assessment attempt",
     *     tags={"Assessment Attempts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Assessment Attempt ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/AssessmentAttempt"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Assessment Attempt not found"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $assessmentAttempt = AssessmentAttempt::find($id);

        if (is_null($assessmentAttempt)) {
            return $this->sendError('Assessment Results Details Not Found');
        }

        return $this->sendResponse(new AssessmentAttemptResource($assessmentAttempt), 'Assessment Results Retrieved successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/assessment-attempts/{id}",
     *     summary="Update an assessment attempt",
     *     tags={"Assessment Attempts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Assessment Attempt ID"
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="job_id", type="integer"),
     *             @OA\Property(property="assessment_id", type="integer"),
     *             @OA\Property(property="assessment_score", type="number"),
     *             @OA\Property(property="practical_score", type="number"),
     *             @OA\Property(property="interview_score", type="number"),
     *             @OA\Property(property="status", type="string", enum={"In Review", "Approved", "Rejected", "Hired"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/AssessmentAttempt")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Assessment Attempt not found"
     *     )
     * )
     */
    public function update(Request $request): JsonResponse
    {
        // Validate input, making all fields nullable to allow for partial updates
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:assessment_attempts,id',
            'job_id' => 'nullable',
            'assessment_id' => 'nullable',
            'assessment_score' => 'nullable|numeric',
            'practical_score' => 'nullable|numeric',
            'interview_score' => 'nullable|numeric',
            'status' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 422);
        }

        $id = $request->input('id');

        // Find the assessment attempt
        $assessmentAttempt = AssessmentAttempt::find($id);

        if (is_null($assessmentAttempt)) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment Attempt Not Found.'
            ], 404);
        }

        // Update fields only if they are provided in the request
        if ($request->has('job_id')) {
            $assessmentAttempt->job_id = $request->job_id;
        }

        if ($request->has('assessment_id')) {
            $assessmentAttempt->assessment_id = $request->assessment_id;
        }

        if ($request->has('assessment_score')) {
            $assessmentAttempt->assessment_score = $request->assessment_score;
        }

        if ($request->has('practical_score')) {
            $assessmentAttempt->practical_score = $request->practical_score;
        }
        if ($request->has('interview_score')) {
            $assessmentAttempt->interview_score = $request->interview_score;
        }
        if ($request->has('status')) {
            $assessmentAttempt->status = $request->status;
        }
        // Save the updated assessment attempt
        $assessmentAttempt->save();

        return response()->json([
            'success' => true,
            'message' => 'Assessment Attempt Updated Successfully.',
            'data' => new AssessmentAttemptResource($assessmentAttempt)
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/assessment-attempts/{id}",
     *     summary="Delete an assessment attempt",
     *     tags={"Assessment Attempts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Assessment Attempt ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/AssessmentAttempt")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Assessment Attempt not found"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        // Find the assessment attempt
        $assessmentAttempt = AssessmentAttempt::find($id);

        if (is_null($assessmentAttempt)) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment Attempt Not Found.'
            ], 404);
        }

        // Delete the assessment attempt
        $assessmentAttempt->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assessment Attempt Deleted Successfully.'
        ]);
    }


    /**
     * @SWG\Get(
     *     path="/api/user/assessment-attempts/user",
     *     summary="Get the assessment results for a specific user",
     *     tags={"Assessment Attempts"},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful response",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="success", type="boolean"),
     *             @SWG\Property(property="data", type="array", @SWG\Items(ref="#/definitions/AssessmentAttempt")),
     *             @SWG\Property(property="message", type="string")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="User not authenticated"
     *     )
     * )
     */
    public function getUserAssessments(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Ensure the user is authenticated
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }


        $applications = AssessmentAttempt::with('user')
            ->where('user_id', $user->id)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Applications Retrieved Successfully',
            'data' => AssessmentAttemptResource::collection($applications)
        ]);
    }


    /**
     * @SWG\Get(
     *     path="/api/user/check-test-attempt ",
     *     summary="Check if a user has attempted a test",
     *     tags={"Assessment Attempts"},
     *     @SWG\Parameter(
     *         name="job_id",
     *         in="formData",
     *         required=true,
     *         type="integer",
     *         description="Job ID"
     *     ),
     *     @SWG\Parameter(
     *         name="assessment_id",
     *         in="formData",
     *         required=true,
     *         type="integer",
     *         description="Assessment ID"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful response",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="success", type="boolean"),
     *             @SWG\Property(property="message", type="string"),
     *             @SWG\Property(property="data", type="object", 
     *                 @SWG\Property(property="attempted", type="boolean"),
     *                 @SWG\Property(property="assessment_score", type="integer"),
     *                 @SWG\Property(property="attempt_date", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Validation Error"
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="User not authenticated"
     *     )
     * )
     */
    public function checkTestAttempt(Request $request): JsonResponse
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|integer',
            'assessment_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Extract request parameters
        $jobId = $request->input('job_id');
        $assessmentId = $request->input('assessment_id');

        // Get authenticated user
        $user = Auth::user();
        Log::info('Authenticated user:', ['user' => $user]);

        // Check if the user has attempted the test
        $attempt = $user->assessmentAttempts()
            ->where('job_id', $jobId)
            ->where('assessment_id', $assessmentId)
            ->first();

        // Check if an attempt was found
        if ($attempt) {
            return response()->json([
                'success' => true,
                'message' => 'User has attempted the test.',
                'data' => [
                    'attempted' => true,
                    'assessment_score' => $attempt->assessment_score,
                    'attempt_date' => $attempt->created_at,
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User has not attempted the test.',
            'data' => [
                'attempted' => false,
            ]
        ]);
    }

    /**
     * @SWG\Get(
     *     path="/api/user/applicants-by-job",
     *     summary="Get applicants by job ID",
     *     tags={"Assessment Attempts"},
     *     @SWG\Parameter(
     *         name="job_id",
     *         in="formData",
     *         required=true,
     *         type="integer",
     *         description="Job ID"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful response",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="success", type="boolean"),
     *             @SWG\Property(property="message", type="string"),
     *             @SWG\Property(property="data", type="array", @SWG\Items(ref="#/definitions/ApplicantsGroupedById"))
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Validation Error"
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
        $jobApplicants = AssessmentAttempt::with('user')
            ->where('job_id', $jobId)
            ->orderBy('assessment_score', 'desc') // or 'asc' for ascending order
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Applicants Retrieved Successfully',
            'data' => ApplicantsGroupedById::collection($jobApplicants)
        ]);
    }
}
