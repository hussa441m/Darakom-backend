<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [  'experience_start', 'admin_comment','user_id','role_id'];

    function user(){
        return $this->belongsTo(User::class);
    }
    function role(){
        return $this->belongsTo(Role::class);
    }

    function documents(){
        return $this->morphMany(Document::class , 'documentable');
    }
    function projectTypes(){
        return $this->belongsToMany(
            ProjectType::class,
            'project_type_role',
            'role_id', // pivot column that references the profile->role_id
            'project_type_id',
            'role_id', // local key on this model that stores the role id
            'id' // related model primary key
        );
    }
    function projects(){
        return $this->hasMany(Project::class ,'performed_by');
    }
}
