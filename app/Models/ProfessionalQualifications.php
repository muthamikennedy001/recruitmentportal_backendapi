<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalQualifications extends Model
{
    use HasFactory;

    protected $fillable=[
        "institution",
        "body",
        "award",
        "professionalCertificate",
    ];
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
