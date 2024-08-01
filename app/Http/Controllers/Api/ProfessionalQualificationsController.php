<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreProfessionalQualificationsRequest;
use App\Http\Requests\UpdateProfessionalQualificationsRequest;
use App\Http\Resources\ProfessionalQualificationsResource;
use App\Models\ProfessionalQualifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Professional Qualification",
 *     description="API endpoints for managing professional qualification"
 * )
 */

class ProfessionalQualificationsController extends Basecontroller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/user/professionalQualifications",
     *     summary="Get all professional qualifications",
     *     tags={"Professional Qualification"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ProfessionalQualifications"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $professionalQualification = ProfessionalQualifications::all();
        return $this->sendResponse(ProfessionalQualificationsResource::collection($professionalQualification), 'Professional Qualification Details Retrieved Successfully');
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/api/user/professionalQualifications",
     *     summary="Store new professional qualifications",
     *     tags={"Professional Qualification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="institution", type="string", example="Harvard University"),
     *             @OA\Property(property="body", type="string", example="Engineering Department"),
     *             @OA\Property(property="award", type="string", example="Bachelor of Science"),
     *             @OA\Property(property="professionalCertificate", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Professional qualification created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Professional Qualification Details Created Successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/ProfessionalQualifications")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $input = $request->all();

        // Validate input
        $validator = Validator::make($input, [
            "institution" => 'required',
            "body" => 'required',
            "award" => 'required',
            "professionalCertificate" => 'nullable|file|mimes:pdf|max:2048'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Check if the user already has a professional qualification record
        $user = Auth::user();
        $existingQualification = $user->professionalQualifications()->first();

        if ($existingQualification) {
            return $this->sendError('User already has a professional qualification.', ['existing_record' => new ProfessionalQualificationsResource($existingQualification)]);
        }

        // Initialize path to null
        $path = null;

        // Store the file if it exists
        if ($request->hasFile('professionalCertificate')) {
            $path = Storage::disk('public')->put('applicant_professional_certificates', $request->file('professionalCertificate'));
        }

        // Create new professional qualification
        $professionalQualification = $user->professionalQualifications()->create([
            "institution" => $request->institution,
            "body" => $request->body,
            "award" => $request->award,
            "professionalCertificate" => $path
        ]);

        return $this->sendResponse(new ProfessionalQualificationsResource($professionalQualification), 'Professional Qualification Details Created Successfully');
    }


    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/user/professionalQualifications/{id}",
     *     summary="Get specific professional qualification",
     *     tags={"Professional Qualification"},
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
     *             @OA\Property(property="data", ref="#/components/schemas/ProfessionalQualifications")
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
        $professionalQualification = ProfessionalQualifications::find($id);
        if (is_null($professionalQualification)) {
            return $this->sendError('Professional Qualification Details Not Found');
        }

        return $this->sendResponse(new ProfessionalQualificationsResource($professionalQualification), 'Professional Qualification Details Retrieved sucessfully');
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/user/professionalQualifications/{id}",
     *     summary="Update specific professional qualification",
     *     tags={"Professional Qualification"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="institution", type="string", example="Harvard University"),
     *             @OA\Property(property="body", type="string", example="Engineering Department"),
     *             @OA\Property(property="award", type="string", example="Bachelor of Science"),
     *             @OA\Property(property="professionalCertificate", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Professional qualification updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Professional Qualification updated successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/ProfessionalQualifications")
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
        // Find the ProfessionalQualifications by ID
        $professionalQualifications = ProfessionalQualifications::find($id);

        // Check if the record exists
        if (!$professionalQualifications) {
            return $this->sendError('Record not found.', [], 404);
        }

        // Get all request input
        $input = $request->all();

        // Validate the request input
        $validator = Validator::make($input, [
            "institution" => 'required|string',
            "body" => 'required|string',
            "award" => 'required|string',
            "professionalCertificate" => 'nullable|file|mimes:pdf|max:2048'
        ]);

        // Return validation errors, if any
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Initialize certificate path with existing path
        $path = $professionalQualifications->professionalCertificate ?? null;

        // Check if a new certificate file is uploaded
        if ($request->hasFile('professionalCertificate')) {
            // Delete the old certificate if it exists
            if ($professionalQualifications->professionalCertificate) {
                Storage::disk('public')->delete($professionalQualifications->professionalCertificate);
            }
            // Store the new certificate
            $path = Storage::disk('public')->put('applicant_professional_certificates', $request->file('professionalCertificate'));
        }

        // Update the record fields
        $professionalQualifications->institution = $input['institution'];
        $professionalQualifications->body = $input['body'];
        $professionalQualifications->award = $input['award'];
        $professionalQualifications->professionalCertificate = $path;

        // Save the updated record
        $professionalQualifications->save();

        // Return a success response with the updated resource
        return $this->sendResponse(new ProfessionalQualificationsResource($professionalQualifications), 'Professional Qualification updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */


    /**
     * @OA\Delete(
     *     path="/api/user/professionalQualifications/{id}",
     *     summary="Delete specific professional qualification",
     *     tags={"Professional Qualification"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Professional qualification deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Professional Qualification Information Deleted Successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     )
     * )
     */
    public function destroy(ProfessionalQualifications $professionalQualifications)
    {
        //
        //
        if ($professionalQualifications->professionalCertificate) {
            Storage::disk('public')->delete($professionalQualifications->professionalCertificate);
        }
        $professionalQualifications->delete();

        return $this->sendResponse([], 'Professional Qualification Information Deleted Successfully.');
    }
}
