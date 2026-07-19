<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    public $timestamps = false;

    protected $fillable = [
    'name',
    'display_name',
];

    public function artisanServices()
    {
        return $this->hasMany(ArtisanService::class);
    }
    public function profiles()
    {
        return $this->belongsToMany(
         Profile::class,
        'artisan_services',
        'service_category_id',
        'profile_id'
     );
    }
}