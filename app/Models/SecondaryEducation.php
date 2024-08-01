<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecondaryEducation extends Model
{
    use HasFactory;

    protected $fillable=[
        "school",
        "kcseYear",
        "grade",
        "kcseCertificate",
    ];
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
