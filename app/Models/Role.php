<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name',
    ];

    public function projectTypes()
    {
       return $this->belongsToMany(ProjectType::class,'project_type_role','role_id','project_type_id');
    }
    function profiles(){
        return $this->hasMany(Profile::class);
    }
}
