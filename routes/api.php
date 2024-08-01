<?php

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\ApplicantController;
use App\Http\Controllers\Api\AssessmentAttemptController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HighestEducationLevelController;
use App\Http\Controllers\Api\PersonalDetailsController;
use App\Http\Controllers\Api\ProfessionalQualificationsController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\SecondaryEducationController;
use App\Http\Controllers\Api\ShortlistedApplicantController;
use App\Http\Controllers\Api\UserProfileController;
use App\Models\ProfessionalQualifications;
use Illuminate\Support\Facades\Route;

//open routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'passwordReset'])->name('password.reset');
Route::post('/forgot-password', [ResetPasswordController::class, 'passwordEmail'])->name('password.email');
Route::post('/reset-password', [ResetPasswordController::class, 'passwordUpdate'])->name('password.update');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['signed'])->name('verification.verify');

Route::post('/email/verification-notification', [AuthController::class, 'verifyHandler'])->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');
Route::get('/email/verify', [AuthController::class, 'verifyNotice'])->middleware('auth:sanctum')->name('verification.notice');
Route::get('/user/verification-status', [AuthController::class, 'checkVerificationStatus'])->middleware('auth:sanctum');

Route::get('/applicants', [ApplicantController::class, 'index']);
Route::get('/applicants/{applicant}', [ApplicantController::class, 'show']);
Route::get('user/jobshortlistedapplicants', [ShortlistedApplicantController::class, 'getApplicantsByJobId']);
Route::get('user/applicants-by-job', [ShortlistedApplicantController::class, 'getApplicantsByJob']);
Route::get('user/applications-by-user', [ShortlistedApplicantController::class, 'getApplicationsByUser']);
Route::get('user/shortlistedapplicants', [ShortlistedApplicantController::class, 'indexGroupedByJob']);
Route::get('user/shortlistedapplicants/{id}', [ShortlistedApplicantController::class, 'show']);
Route::put('user/updateShortlistedApplicant', [ShortlistedApplicantController::class, 'update']);
Route::get('user/all-jobs-applicants', [AssessmentAttemptController::class, 'indexGroupedByJob']);
Route::put('user/updateAssessmentAttempt', [AssessmentAttemptController::class, 'update']);
Route::get('user/specific-job-applicants', [AssessmentAttemptController::class, 'getApplicantsByJob']);


Route::group([
  "middleware" => ['auth:sanctum']
], function () {
  Route::get('profile', [AuthController::class, 'profile']);
  Route::post('logout', [AuthController::class, 'logout']);
  Route::get('user/check-test-attempt', [AssessmentAttemptController::class, 'checkTestAttempt']);
  Route::put('/update-professional-qualifications/{id}', [ProfessionalQualificationsController::class, 'updateProfessionalQualifications']);
  Route::resource('user/personaldetails', PersonalDetailsController::class);
  Route::get('user/educationdetails', [UserProfileController::class, 'getEducationData']);
  Route::resource('user/highestEducationLevel', HighestEducationLevelController::class);
  Route::resource('user/secondaryEducation', SecondaryEducationController::class);
  Route::resource('user/professionalQualification', ProfessionalQualificationsController::class);

  Route::get('user/assessment-attempts', [AssessmentAttemptController::class, 'index']);
  Route::post('user/assessment-attempts', [AssessmentAttemptController::class, 'store']);
  Route::get('user/assessment-attempts/{id}', [AssessmentAttemptController::class, 'show']);
  // Route::put('user/assessment-attempts/{id}', [AssessmentAttemptController::class, 'update']);
  Route::delete('user/assessment-attempts/{id}', [AssessmentAttemptController::class, 'destroy']); // Route for deleting assessment attempts
  Route::get('user/assessment-attempts', [AssessmentAttemptController::class, 'getUserAssessments']);

  Route::post('user/shortlistedapplicants', [ShortlistedApplicantController::class, 'store']);
  Route::get('/user/my-applications', [ShortlistedApplicantController::class, 'getApplicationsByUserId']);
  Route::get('user/specificJobApplication/{applicationId}', [ShortlistedApplicantController::class, 'show']);
  Route::delete('user/shortlistedapplicants/{id}', [ShortlistedApplicantController::class, 'destroy']); // Route for deleting assessment attempts
});
