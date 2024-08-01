<?php

namespace App\Http\Controllers\Api;


use App\Models\ProfessionalQualifications;
use App\Models\SecondaryEducation;
use App\Models\HighestEducationLevel;
use App\Models\PersonalDetails;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="User Profile ",
 *     description="API endpoints for managing user profile"
 * )
 */

class UserProfileController extends Basecontroller // Extend BaseController
{
    /**
     * @OA\Get(
     *     path="/api/user/educationdetails",
     *     summary="Get educational data of the authenticated user",
     *     tags={"User Profile"},
     *     @OA\Response(
     *         response=200,
     *         description="Educational data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Educational data retrieved successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="professional_qualifications",
     *                     type="array",
     *                     @OA\Items(type="object")
     *                 ),
     *                 @OA\Property(
     *                     property="secondary_education",
     *                     type="array",
     *                     @OA\Items(type="object")
     *                 ),
     *                 @OA\Property(
     *                     property="highest_education_level",
     *                     type="array",
     *                     @OA\Items(type="object")
     *                 ),
     *                 @OA\Property(
     *                     property="personal_details",
     *                     type="array",
     *                     @OA\Items(type="object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User not authenticated."
     *             )
     *         )
     *     )
     * )
     */
    public function getEducationData()
    {
        $user = auth()->user(); // Retrieve the authenticated user
        Log::info('Fetching educational data for user.', ['user_id' => $user->id]);

        // Fetch personal details
        $personalDetails = PersonalDetails::where('user_id', $user->id)->get();
        if ($personalDetails->isEmpty()) {
            Log::info('No Personal Details found for user.', ['user_id' => $user->id]);
        }
        // Fetch professional qualifications
        $professionalQualifications = ProfessionalQualifications::where('user_id', $user->id)->get();
        if ($professionalQualifications->isEmpty()) {
            Log::info('No professional qualifications found for user.', ['user_id' => $user->id]);
        }

        // Fetch secondary education data
        $secondaryEducation = SecondaryEducation::where('user_id', $user->id)->get();
        if ($secondaryEducation->isEmpty()) {
            Log::info('No secondary education records found for user.', ['user_id' => $user->id]);
        }

        // Fetch highest education level data
        $highestEducationLevel = HighestEducationLevel::where('user_id', $user->id)->get();
        if ($highestEducationLevel->isEmpty()) {
            Log::info('No highest education level records found for user.', ['user_id' => $user->id]);
        }

        // Prepare data to return
        $data = [
            'professional_qualifications' => $professionalQualifications,
            'secondary_education' => $secondaryEducation,
            'highest_education_level' => $highestEducationLevel,
            'personal_details' => $personalDetails,
        ];

        // Return the data with a success response
        return $this->sendResponse($data, 'Educational data retrieved successfully');
    }
}
