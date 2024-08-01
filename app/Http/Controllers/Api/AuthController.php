<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Tag(
 *     name="Authentication and Email Verification",
 *     description="API endpoints for managing authentication"
 * )
 */
/** 
 * @OA\Schema(
 *     schema="User",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class AuthController extends Controller
{


    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful registration",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="access_token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     )
     * )
     */
    public function register(Request $request)
    {

        $fields = $request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'max:255', 'email', 'unique:users'],
            'password' => ['required', 'min:3', 'confirmed']
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
        ]);

        event(new Registered($user));

        $accessToken = $user->createToken('authToken')->accessToken;

        return response()->json(['message' => 'User registered. Check your email for verification link.', 'user' => $user, 'access_token' => $accessToken], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login a user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function login(Request $request)
    {
        //validation
        $request->validate([
            'email' => "required|email|string",
            'password' => "required",
        ]);
        //check if user exists 
        $user = User::where("email", $request->email)->first();

        if (!empty($user)) {
            //user exists
            if (Hash::check($request->password, $user->password)) {
                //password match
                $token = $user->createToken("")->plainTextToken;

                return response()->json([
                    "status" => true,
                    "message" => "User Logged In successfully",
                    "token" => $token,
                    "data" => []
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Invalid Password",
                    "data" => []
                ]);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => "No user registered with that email.",
                "data" => []
            ]);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Get the authenticated user's profile",
     *     tags={"User Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile information",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="profile information"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     )
     * )
     */

    //GET [Auth:token]
    public function profile(Request $request)
    {
        $userData = auth()->user();
        return response()->json([
            "status" => true,
            "message" => "profile information",
            "data" => $userData,
            "id" => auth()->user()->id
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout the authenticated user",
     *     tags={"User Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="user logged out"),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => "user logged out",
            'data' => []
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/email/verify",
     *     summary="Send email verification notice",
     *     tags={"Email Verification"},
     *     @OA\Response(
     *         response=200,
     *         description="Verification email sent",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="We have sent you email. Please verify your email.")
     *         )
     *     )
     * )
     */
    // Email verification notice
    public function verifyNotice()
    {
        return response()->json(['message' => ' We have sent you email. Please verify your email.'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/email/verify/{id}/{hash}",
     *     summary="Verify email",
     *     tags={"Email Verification"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="hash",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirection to the dashboard after email verification"
     *     )
     * )
     */

    public function verifyEmail(Request $request): RedirectResponse
    {
        $user = User::find($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            return redirect(env('FRONT_URL') . '/dashboard');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect(env('FRONT_URL') . '/dashboard');
    }

    /**
     * @OA\Post(
     *     path="/api/email/verification-notification",
     *     summary="Resend email verification link",
     *     tags={"Email Verification"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Verification status",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Verification link sent!"),
     *             
     *         )
     *     )
     * )
     */
    public function verifyHandler(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'User Has Already verified!'], 200);
        } else {
            $request->user()->sendEmailVerificationNotification();
            return response()->json(['message' => 'Verification link sent!'], 200);
        }
    }


    /**
     * @OA\Get(
     *     path="api/user/verification-status",
     *     summary="Check email verification status",
     *     tags={"Email Verification"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Verification status",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="verified", type="boolean", example=true)
     *         )
     *     )
     * )
     */

    public function checkVerificationStatus(Request $request)
    {
        return response()->json(['verified' => $request->user()->hasVerifiedEmail()]);
    }
}
