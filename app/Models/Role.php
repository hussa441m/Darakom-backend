<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name',
    ];

    function projectTypes(){
        return $this->belongsToMany(ProjectType::class);
    }
    function profiles(){
        return $this->hasMany(Profile::class);
    }
}
