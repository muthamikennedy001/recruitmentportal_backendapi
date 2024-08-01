<?php

namespace App\Models;



use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements CanResetPassword, MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = env('FRONT_URL') . '/reset-password' . '/' . $token;

        $this->notify(new ResetPasswordNotification($url));
    }

    public function personalDetails(): HasOne
    {
        return $this->hasOne(PersonalDetails::class);
    }
    public function highestEducationLevels(): HasOne
    {
        return $this->hasOne(HighestEducationLevel::class);
    }
    public function secondaryEducation(): HasOne
    {
        return $this->hasOne(SecondaryEducation::class);
    }
    public function professionalQualifications(): HasMany
    {
        return $this->hasMany(ProfessionalQualifications::class);
    }
    public function shortlistedApplicants(): HasMany
    {
        return $this->hasMany(ShortlistedApplicants::class);
    }
    public function assessmentAttempts(): HasMany
    {
        return $this->hasMany(AssessmentAttempt::class);
    }
}
