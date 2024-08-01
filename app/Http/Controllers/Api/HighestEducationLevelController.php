<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Basecontroller as Basecontroller;
use App\Http\Requests\StoreHighestEducationLevelRequest;
use App\Http\Requests\UpdateHighestEducationLevelRequest;
use App\Http\Resources\HighestEducationLevelResource;
use App\Models\HighestEducationLevel;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Highest Education Level",
 *     description="API endpoints for managing Highest Education Level records"
 * )
 */

class HighestEducationLevelController extends Basecontroller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Get(
     *     path="/api/user/highestEducationLevel",
     *     summary="Get all highest education levels",
     *     tags={"Highest Education Level"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/HighestEducationLevel"))
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        //
        $highestEducationLevel = HighestEducationLevel::all();
        return $this->sendResponse(HighestEducationLevelResource::collection($highestEducationLevel), 'Highest Education Level Details Retrieved Successfully');
    }


    /**
     * Store a newly created resource in storage.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Post(
     *     path="/api/user/highestEducationLevel",
     *     summary="Store a new highest education level",
     *     tags={"Highest Education Level"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="institution", type="string"),
     *             @OA\Property(property="course", type="string"),
     *             @OA\Property(property="graduationYear", type="string"),
     *             @OA\Property(property="grade", type="string"),
     *             @OA\Property(property="certificate", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Highest education level created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/HighestEducationLevel")
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
            "institution" => 'required',
            "course" => 'required',
            "graduationYear" => 'required',
            "grade" => 'required',
            "certificate" => 'nullable|file|mimes:pdf|max:2048'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Check if the user already has a highest education level record
        $user = Auth::user();
        $existingEducationLevel = $user->highestEducationLevels()->first(); // Adjust if it's a one-to-many relationship

        if ($existingEducationLevel) {
            return $this->sendError('User already has a highest education level record.', [
                'existing_record' => new HighestEducationLevelResource($existingEducationLevel)
            ]);
        }

        // Initialize path to null
        $path = null;

        // Store the file if it exists
        if ($request->hasFile('certificate')) {
            $path = Storage::disk('public')->put('applicant_certificates', $request->file('certificate'));
        }

        // Create new highest education level record
        $highestEducationLevel = $user->highestEducationLevels()->create([
            "institution" => $request->institution,
            "course" => $request->course,
            "graduationYear" => $request->graduationYear,
            "grade" => $request->grade,
            "certificate" => $path
        ]);

        return $this->sendResponse(new HighestEducationLevelResource($highestEducationLevel), 'Highest Education Level Details Created Successfully');
    }

    /**
     * Display the specified resource.
     */

    /**
     * @OA\Get(
     *     path="/api/user/highestEducationLevel/{id}",
     *     summary="Get a specific highest education level",
     *     tags={"Highest Education Level"},
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
     *             @OA\Property(property="data", ref="#/components/schemas/HighestEducationLevel")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     )
     * )
     */
    public function show($id)
    {
        //
        $highestEducationLevel = HighestEducationLevel::find($id);
        if (is_null($highestEducationLevel)) {
            return $this->sendError('Highest Education Level Details Not Found');
        }

        return $this->sendResponse(new HighestEducationLevelResource($highestEducationLevel), 'Highest Education Level Details Retrieved sucessfully');
    }



    /**
     * Update the specified resource in storage.
     */

    /**
     * @OA\Put(
     *     path="/api/user/highestEducationLevel/{id}",
     *     summary="Update a specific highest education level",
     *     tags={"Highest Education Level"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="institution", type="string"),
     *             @OA\Property(property="course", type="string"),
     *             @OA\Property(property="graduationYear", type="string"),
     *             @OA\Property(property="grade", type="string"),
     *             @OA\Property(property="certificate", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Highest education level updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/HighestEducationLevel")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        // Find the HighestEducationLevel by ID
        // Log the incoming request data
        \Log::info('Incoming request data:', $request->all());
        $highestEducationLevel = HighestEducationLevel::find($id);

        // Check if the record exists
        if (!$highestEducationLevel) {
            return $this->sendError('Record not found.', [], 404);
        }

        // Get all request input
        $input = $request->all();

        // Validate the request input
        $validator = Validator::make($input, [
            "institution" => 'required|string',
            "course" => 'required|string',
            "graduationYear" => 'required',
            "grade" => 'required|string',
            "certificate" => 'nullable|file|mimes:pdf|max:2048'
        ]);

        // Return validation errors, if any
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Initialize certificate path with existing path
        $path = $highestEducationLevel->certificate ?? null;

        // Check if a new certificate file is uploaded
        if ($request->hasFile('certificate')) {
            // Delete the old certificate if it exists
            if ($highestEducationLevel->certificate) {
                Storage::disk('public')->delete($highestEducationLevel->certificate);
            }
            // Store the new certificate
            $path = Storage::disk('public')->put('applicant_certificates', $request->file('certificate'));
        }

        // Update the record fields
        $highestEducationLevel->institution = $input['institution'];
        $highestEducationLevel->course = $input['course'];
        $highestEducationLevel->graduationYear = $input['graduationYear'];
        $highestEducationLevel->grade = $input['grade'];
        $highestEducationLevel->certificate = $path;

        // Save the updated record
        $highestEducationLevel->save();

        // Return a success response with the updated resource
        return $this->sendResponse(new HighestEducationLevelResource($highestEducationLevel), 'Highest education level updated successfully.');
    }


    /**
     * @OA\Delete(
     *     path="/api/user/highestEducationLevel/{id}",
     *     summary="Delete a specific highest education level",
     *     tags={"Highest Education Level"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Highest education level deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     )
     * )
     */
    public function destroy(HighestEducationLevel $highestEducationLevel)
    {
        //
        if ($highestEducationLevel->certificate) {
            Storage::disk('public')->delete($highestEducationLevel->certificate);
        }
        $highestEducationLevel->delete();

        return $this->sendResponse([], 'High School Deleted Successfully.');
    }
}
