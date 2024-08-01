<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Basecontroller;
use App\Http\Requests\StoreSecondaryEducationRequest;
use App\Http\Requests\UpdateSecondaryEducationRequest;
use App\Http\Resources\SecondaryEducationResource;
use App\Models\SecondaryEducation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @SWG\Tag(
 *     name="Secondary Education",
 *     description="API endpoints for managing secondary education records"
 * )
 */

class SecondaryEducationController extends Basecontroller
{
    /**
     * @OA\Get(
     *     path="/api/user/secondaryEducation",
     *     summary="Get all secondary education records",
     *     tags={"Secondary Education"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SecondaryEducation"))
     *         )
     *     )
     * )
     */

    public function index()
    {
        //

        $secondaryEducation = SecondaryEducation::all();
        return $this->sendResponse(SecondaryEducationResource::collection($secondaryEducation), 'Highest Education Level Details Retrieved Successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/user/secondaryEducation",
     *     summary="Store new secondary education record",
     *     tags={"Secondary Education"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"school", "kcseYear", "grade"},
     *             @OA\Property(property="school", type="string"),
     *             @OA\Property(property="kcseYear", type="integer"),
     *             @OA\Property(property="grade", type="string"),
     *             @OA\Property(property="kcseCertificate", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Secondary education record created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/SecondaryEducation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        // Validate input
        $validator = Validator::make($input, [
            'school' => 'required',
            'kcseYear' => 'required',
            'grade' => 'required',
            'kcseCertificate' => 'nullable|file|mimes:pdf|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if the user already has a secondary education record
        $user = Auth::user();
        $existingSecondaryEducation = $user->SecondaryEducation()->first();

        if ($existingSecondaryEducation) {
            return response()->json([
                'success' => false,
                'message' => 'User already has a secondary education record.',
                'existing_record' => new SecondaryEducationResource($existingSecondaryEducation)
            ], 400);
        }

        // Initialize path to null
        $path = null;

        // Store the file if it exists
        if ($request->hasFile('kcseCertificate')) {
            $path = $request->file('kcseCertificate')->store('applicant_kcse_certificates', 'public');
        }

        // Create new secondary education record
        $secondaryEducation = $user->SecondaryEducation()->create([
            'school' => $request->school,
            'kcseYear' => $request->kcseYear,
            'grade' => $request->grade,
            'kcseCertificate' => $path
        ]);

        return response()->json([
            'success' => true,
            'message' => 'High School Details Created Successfully',
            'data' => new SecondaryEducationResource($secondaryEducation)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/user/secondaryEducation/{id}",
     *     summary="Get specific secondary education record",
     *     tags={"Secondary Education"},
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
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/SecondaryEducation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found"
     *     )
     * )
     */
    public function show($id)
    {
        //
        $secondaryEducation = SecondaryEducation::find($id);
        if (is_null($secondaryEducation)) {
            return $this->sendError('Secondary Education Details Not Found');
        }

        return $this->sendResponse(new SecondaryEducationResource($secondaryEducation), 'Secondary Education Details Retrieved sucessfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SecondaryEducation $secondaryEducation)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/api/user/secondaryEducation/{id}",
     *     summary="Update specific secondary education record",
     *     tags={"Secondary Education"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"school", "kcseYear", "grade"},
     *             @OA\Property(property="school", type="string"),
     *             @OA\Property(property="kcseYear", type="integer"),
     *             @OA\Property(property="grade", type="string"),
     *             @OA\Property(property="kcseCertificate", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Secondary education record updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/SecondaryEducation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        // Find the SecondaryEducation by ID
        $secondaryEducation = SecondaryEducation::find($id);

        // Check if the record exists
        if (!$secondaryEducation) {
            return $this->sendError('Record not found.', [], 404);
        }

        // Get all request input
        $input = $request->all();

        // Validate the request input
        $validator = Validator::make($input, [
            "school" => 'required|string',
            "kcseYear" => 'required|integer|min:1900|max:' . date('Y'),
            "grade" => 'required|string',
            "kcseCertificate" => 'nullable|file|mimes:pdf|max:2048'
        ]);

        // Return validation errors, if any
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Initialize certificate path with existing path
        $path = $secondaryEducation->kcseCertificate ?? null;

        // Check if a new certificate file is uploaded
        if ($request->hasFile('kcseCertificate')) {
            // Delete the old certificate if it exists
            if ($secondaryEducation->kcseCertificate) {
                Storage::disk('public')->delete($secondaryEducation->kcseCertificate);
            }
            // Store the new certificate
            $path = Storage::disk('public')->put('applicant_kcse_certificates', $request->file('kcseCertificate'));
        }

        // Update the record fields
        $secondaryEducation->school = $input['school'];
        $secondaryEducation->kcseYear = $input['kcseYear'];
        $secondaryEducation->grade = $input['grade'];
        $secondaryEducation->kcseCertificate = $path;

        // Save the updated record
        $secondaryEducation->save();

        // Return a success response with the updated resource
        return $this->sendResponse(new SecondaryEducationResource($secondaryEducation), 'Secondary education updated successfully.');
    }


    /**
     * @OA\Delete(
     *     path="/api/user/secondaryEducation/{id}",
     *     summary="Delete specific secondary education record",
     *     tags={"Secondary Education"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Secondary education record deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found"
     *     )
     * )
     */
    public function destroy(SecondaryEducation $secondaryEducation)
    {
        //
        if ($secondaryEducation->kcseCertificate) {
            Storage::disk('public')->delete($secondaryEducation->kcseCertificate);
        }
        $secondaryEducation->delete();

        return $this->sendResponse([], 'High school Details Deleted Successfully.');
    }
}
