<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Basecontroller as BaseController;
use App\Http\Resources\PersonalDetailsResource;
use App\Models\PersonalDetails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Personal Details",
 *     description="API endpoints for managing personal details"
 * )
 */
class PersonalDetailsController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/personaldetails",
     *     summary="Get all personal details",
     *     tags={"Personal Details"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PersonalDetails"))
     *         )
     *     )
     * )
     */

    public function index(): JsonResponse
    {
        $personalDetails = PersonalDetails::all();
        return $this->sendResponse(PersonalDetailsResource::collection($personalDetails), 'Personal Details Retrieved Successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/personaldetails",
     *     summary="Store new personal details",
     *     tags={"Personal Details"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="firstname", type="string"),
     *             @OA\Property(property="lastname", type="string"),
     *             @OA\Property(property="nationalId", type="string"),
     *             @OA\Property(property="contactNo", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="gender", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Personal details created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/PersonalDetails")
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

        $validator = Validator::make($input, [
            "firstname" => 'required',
            "lastname" => 'required',
            "nationalId" => 'required',
            "contactNo" => 'required',
            "address" => 'required',
            "gender" => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Check if the user already has personal details
        $user = Auth::user();
        $existingDetails = $user->PersonalDetails()->first();

        if ($existingDetails) {
            return $this->sendError('User already has personal details.', ['existing_record' => new PersonalDetailsResource($existingDetails)]);
        }

        // Create new personal details if no record exists
        $personalDetails = $user->PersonalDetails()->create([
            "firstname" => $request->firstname,
            "lastname" => $request->lastname,
            "nationalId" => $request->nationalId,
            "contactNo" => $request->contactNo,
            "address" => $request->address,
            "gender" => $request->gender
        ]);

        return $this->sendResponse(new PersonalDetailsResource($personalDetails), 'Personal Details Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/personaldetails/{id}",
     *     summary="Get specific personal details",
     *     tags={"Personal Details"},
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
     *             @OA\Property(property="data", ref="#/components/schemas/PersonalDetails")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $personalDetails = PersonalDetails::find($id);
        if (is_null($personalDetails)) {
            return $this->sendError('Details Not Found');
        }
        return $this->sendResponse(new PersonalDetailsResource($personalDetails), 'Personal Details Retrieved Successfully');
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * @OA\Put(
     *     path="/api/personaldetails/{id}",
     *     summary="Update specific personal details",
     *     tags={"Personal Details"},
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
     *             @OA\Property(property="firstname", type="string"),
     *             @OA\Property(property="lastname", type="string"),
     *             @OA\Property(property="nationalId", type="string"),
     *             @OA\Property(property="contactNo", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="gender", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Personal details updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/PersonalDetails")
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
    public function update(Request $request, $id): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [

            "firstname" => 'required',
            "lastname" => 'required',
            "nationalId" => 'required',
            "contactNo" => 'required',
            "address" => 'required',
            "gender" => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $personalDetails = PersonalDetails::find($id);
        if (is_null($personalDetails)) {
            return $this->sendError('Details Not Found');
        }


        $personalDetails->firstname = $input['firstname'];
        $personalDetails->lastname = $input['lastname'];
        $personalDetails->nationalId = $input['nationalId'];
        $personalDetails->contactNo = $input['contactNo'];
        $personalDetails->address = $input['address'];
        $personalDetails->gender = $input['gender'];
        $personalDetails->save();

        return $this->sendResponse(new PersonalDetailsResource($personalDetails), "Personal Details Updated Successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */

    /**
     * @OA\Delete(
     *     path="/api/personaldetails/{id}",
     *     summary="Delete specific personal details",
     *     tags={"Personal Details"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Personal details deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $personalDetails = PersonalDetails::find($id);
        if (is_null($personalDetails)) {
            return $this->sendError('Details Not Found');
        }

        $personalDetails->delete();

        return $this->sendResponse([], 'Personal Details Deleted Successfully.');
    }
}
