<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectInvitation extends Model
{
    protected $fillable = [
        'project_id',
        'provider_profile_id',
        'status',
        'expires_at',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function provider()
    {
        return $this->belongsTo(Profile::class, 'provider_profile_id');
    }
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}