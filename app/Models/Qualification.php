<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    protected $fillable = [
        'name',
        'image',
        'profile_id',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}