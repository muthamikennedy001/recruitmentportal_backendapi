<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalDetails extends Model
{
    use HasFactory;
    protected $fillable=[ 
        "firstname",
        "lastname",
        "nationalId",
        "contactNo",
        "address",
        "gender",
    ];
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
