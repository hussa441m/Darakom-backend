<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreviousWork extends Model
{
    protected $fillable = [
        'title',
        'description',
        'location',
        'date',
        'profile_id'
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function images()
    {
        return $this->hasMany(PreviousWorkImage::class);
    }
}