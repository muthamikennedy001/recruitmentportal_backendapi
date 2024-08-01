<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HighestEducationLevel extends Model
{
    use HasFactory;
    
    protected $fillable=[
        "institution",
        "course",
        "graduationYear",
        "grade",
        "certificate",
       
    ];
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
