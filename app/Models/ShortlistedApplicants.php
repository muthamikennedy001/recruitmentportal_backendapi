<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShortlistedApplicants extends Model
{
    use HasFactory;
    protected $fillable=[
        "job_id",
        "assessment_id", 
        "assessment_score",
        "practical_score",
        "interview_score",
        'status',
    ];
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
