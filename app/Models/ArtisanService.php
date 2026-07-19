<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArtisanService extends Model
{
    protected $fillable = [
        'profile_id',
        'service_category_id',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}