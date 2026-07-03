<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name',
    ];
    function projects(){
        return $this->hasMany(Project::class);
    }
        function roles(){
        return $this->belongsToMany(Role::class);
    }

}
