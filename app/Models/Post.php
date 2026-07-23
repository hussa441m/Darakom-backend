<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'description',
        'profile_id',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function images()
    {
        return $this->hasMany(PostImage::class);
    }
}